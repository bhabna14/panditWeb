<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeFund;
use App\Models\OfficeTransaction;

class OfficeLedgerController extends Controller
{

    public function index()
    {
        return view('admin.office-ledger-transaction');
    }

    public function filter(Request $request)
    {
        try {
            $from = $request->query('from_date');
            $to   = $request->query('to_date');
            $cat  = $request->query('category');

            // ------- Funds (IN) via OfficeFund -------
            $fundsQ = OfficeFund::query();
            if ($from) $fundsQ->whereDate('date', '>=', $from);
            if ($to)   $fundsQ->whereDate('date', '<=', $to);
            if ($cat)  $fundsQ->where('categories', $cat);

            // Select only the columns we need
            $funds = $fundsQ->get([
                'id',
                'date',
                'amount',
                'mode_of_payment',
                'paid_by',
                'received_by',
                'description',
                'categories',
            ]);

            // ------- Payments (OUT) via OfficeTransaction -------
            $payQ = OfficeTransaction::query();
            if ($from) $payQ->whereDate('date', '>=', $from);
            if ($to)   $payQ->whereDate('date', '<=', $to);
            if ($cat)  $payQ->where('categories', $cat);

            $payments = $payQ->get([
                'id',
                'date',
                'amount',
                'mode_of_payment',
                'paid_by',
                'description',
                'categories',
            ]);

            // ------- Build ledger rows (robust numeric casting) -------
            $rows = [];

            // IN rows from OfficeFund
            foreach ($funds as $f) {
                $amt = is_null($f->amount) ? 0.0 : (float) str_replace([',', '₹', ' '], '', (string) $f->amount);
                $rows[] = [
                    'sl'          => 0,
                    'date'        => Carbon::parse($f->date)->format('Y-m-d'),
                    'category'    => $f->categories ?: null,
                    'direction'   => 'in',
                    'amount'      => round($amt, 2),
                    'mode'        => $f->mode_of_payment ?? null,   // 'cash'|'upi'...
                    'paid_by'     => $f->paid_by ?? null,           // who gave the fund
                    'received_by' => $f->received_by ?? null,       // who received the fund
                    'description' => $f->description ?? '',
                    'source'      => 'fund',
                    'source_id'   => $f->id,
                ];
            }

            // OUT rows from OfficeTransaction
            foreach ($payments as $p) {
                $amt = is_null($p->amount) ? 0.0 : (float) str_replace([',', '₹', ' '], '', (string) $p->amount);
                $rows[] = [
                    'sl'          => 0,
                    'date'        => Carbon::parse($p->date)->format('Y-m-d'),
                    'category'    => $p->categories ?? null,
                    'direction'   => 'out',
                    'amount'      => round($amt, 2),
                    'mode'        => $p->mode_of_payment ?? null,   // 'cash'|'upi'
                    'paid_by'     => $p->paid_by ?? null,           // payer for the expense
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

            // Re-number SL after sort
            foreach ($rows as $i => &$r) $r['sl'] = $i + 1;

            // ------- Metrics (totals) -------
            $inTotal  = 0.0;
            $outTotal = 0.0;
            foreach ($rows as $r) {
                if ($r['direction'] === 'in')  $inTotal  += $r['amount'];
                if ($r['direction'] === 'out') $outTotal += $r['amount'];
            }
            $netTotal = $inTotal - $outTotal;

            // ------- Aggregations -------

            // 1) Category-wise received/spent across funds & payments
            $categoryAgg = []; // [category => ['received'=>x, 'spent'=>y, 'net'=>z]]
            foreach ($rows as $r) {
                $key = $r['category'] ?? 'uncategorized';
                if (!isset($categoryAgg[$key])) {
                    $categoryAgg[$key] = ['received' => 0.0, 'spent' => 0.0, 'net' => 0.0];
                }
                if ($r['direction'] === 'in')  $categoryAgg[$key]['received'] += $r['amount'];
                if ($r['direction'] === 'out') $categoryAgg[$key]['spent']    += $r['amount'];
            }
            foreach ($categoryAgg as $k => $v) {
                $categoryAgg[$k]['received'] = round($v['received'], 2);
                $categoryAgg[$k]['spent']    = round($v['spent'], 2);
                $categoryAgg[$k]['net']      = round($v['received'] - $v['spent'], 2);
            }

            // 2) Fund-wise totals (by fund receiver)
            $fundAgg = []; // [received_by => total_in]
            foreach ($rows as $r) {
                if ($r['source'] === 'fund') {
                    $who = $r['received_by'] ?? 'unknown';
                    if (!isset($fundAgg[$who])) $fundAgg[$who] = 0.0;
                    $fundAgg[$who] += $r['amount'];
                }
            }
            foreach ($fundAgg as $k => $v) $fundAgg[$k] = round($v, 2);

            // 3) Payments totals by payer
            $payerAgg = []; // [paid_by => total_out]
            foreach ($rows as $r) {
                if ($r['source'] === 'payment') {
                    $payer = $r['paid_by'] ?? 'unknown';
                    if (!isset($payerAgg[$payer])) $payerAgg[$payer] = 0.0;
                    $payerAgg[$payer] += $r['amount'];
                }
            }
            foreach ($payerAgg as $k => $v) $payerAgg[$k] = round($v, 2);

            // 4) Mode splits (cash/upi)
            $modeAgg = [
                'cash_in' => 0.0, 'cash_out' => 0.0,
                'upi_in'  => 0.0, 'upi_out'  => 0.0,
            ];
            foreach ($rows as $r) {
                $mode = strtolower((string) ($r['mode'] ?? ''));
                if ($mode === 'cash') {
                    if ($r['direction'] === 'in')  $modeAgg['cash_in'] += $r['amount'];
                    if ($r['direction'] === 'out') $modeAgg['cash_out'] += $r['amount'];
                } elseif ($mode === 'upi') {
                    if ($r['direction'] === 'in')  $modeAgg['upi_in'] += $r['amount'];
                    if ($r['direction'] === 'out') $modeAgg['upi_out'] += $r['amount'];
                }
            }
            foreach ($modeAgg as $k => $v) $modeAgg[$k] = round($v, 2);

            return response()->json([
                'success'       => true,
                'in_total'      => round($inTotal, 2),
                'out_total'     => round($outTotal, 2),
                'net_total'     => round($netTotal, 2),
                'ledger'        => $rows,
                'categoryAgg'   => $categoryAgg,
                'fundAgg'       => $fundAgg,
                'payerAgg'      => $payerAgg,
                'modeAgg'       => $modeAgg,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

}
