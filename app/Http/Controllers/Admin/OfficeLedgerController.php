<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OfficeLedgerController extends Controller
{
  
    public function index()
    {
        return view('admin.office-ledger-transaction');
    }
    // JSON for the ledger table + metrics (used by the ledger page)
    public function filter(Request $request)
    {
        try {
            $from = $request->query('from_date');
            $to   = $request->query('to_date');
            $cat  = $request->query('category');

            // ------- Funds (IN) -------
            $funds = Fund::query();

            if ($from) $funds->whereDate('date', '>=', $from);
            if ($to)   $funds->whereDate('date', '<=', $to);

            // If your Fund has category column, uncomment:
            // if ($cat)  $funds->where('category', $cat);

            // Select only the columns we need; rename if your schema differs
            $funds = $funds->get([
                'id', 'date', 'amount', 'mode', 'received_by', 'description',
                // 'category' // uncomment if exists
            ]);

            // ------- Payments (OUT) -------
            $payments = OfficeTransaction::query();

            if ($from) $payments->whereDate('date', '>=', $from);
            if ($to)   $payments->whereDate('date', '<=', $to);
            if ($cat)  $payments->where('categories', $cat);

            $payments = $payments->get([
                'id', 'date', 'amount', 'mode_of_payment', 'paid_by', 'description', 'categories'
            ]);

            // ------- Build ledger rows -------
            $rows = [];

            // IN rows from funds
            foreach ($funds as $i => $f) {
                $rows[] = [
                    'sl'          => count($rows) + 1,
                    'date'        => Carbon::parse($f->date)->format('Y-m-d'),
                    'category'    => $f->category ?? null,    // null if not present
                    'direction'   => 'in',
                    'amount'      => (float) $f->amount,
                    'mode'        => $f->mode ?? null,        // 'cash'/'upi' etc.
                    'paid_by'     => null,
                    'received_by' => $f->received_by ?? null,
                    'description' => $f->description ?? '',
                    'source'      => 'fund',
                    'source_id'   => $f->id,
                ];
            }

            // OUT rows from payments
            foreach ($payments as $p) {
                $rows[] = [
                    'sl'          => count($rows) + 1,
                    'date'        => Carbon::parse($p->date)->format('Y-m-d'),
                    'category'    => $p->categories ?? null,
                    'direction'   => 'out',
                    'amount'      => (float) $p->amount,
                    'mode'        => $p->mode_of_payment ?? null, // 'cash'/'upi'
                    'paid_by'     => $p->paid_by ?? null,
                    'received_by' => null,
                    'description' => $p->description ?? '',
                    'source'      => 'payment',
                    'source_id'   => $p->id,
                ];
            }

            // Sort DESC by date, then by source_id to stabilize
            usort($rows, function ($a, $b) {
                if ($a['date'] === $b['date']) return $b['source_id'] <=> $a['source_id'];
                return strcmp($b['date'], $a['date']);
            });

            // Metrics
            $inTotal  = array_reduce($rows, fn($c, $r) => $c + ($r['direction'] === 'in'  ? $r['amount'] : 0), 0);
            $outTotal = array_reduce($rows, fn($c, $r) => $c + ($r['direction'] === 'out' ? $r['amount'] : 0), 0);
            $netTotal = $inTotal - $outTotal;

            // Re-number SL after sort
            foreach ($rows as $i => &$r) $r['sl'] = $i + 1;

            return response()->json([
                'success'   => true,
                'in_total'  => round($inTotal, 2),
                'out_total' => round($outTotal, 2),
                'net_total' => round($netTotal, 2),
                'ledger'    => $rows,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }
}
