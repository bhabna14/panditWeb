<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OfficeFund;
use App\Models\OfficeTransaction;

class OfficeLedgerController extends Controller
{
    public function index()
    {
        // Render the category-first ledger page
        return view('admin.office-ledger-transaction');
    }

    public function filter(Request $request)
    {
        try {
            $from = $request->query('from_date');
            $to   = $request->query('to_date');
            $cat  = $request->query('category');

            // ---------- FUNDS (IN) ----------
            $fundsQ = OfficeFund::query();
            if ($from) $fundsQ->whereDate('date', '>=', $from);
            if ($to)   $fundsQ->whereDate('date', '<=', $to);
            if ($cat)  $fundsQ->where('categories', $cat);

            $funds = $fundsQ->get([
                'id','date','amount','mode_of_payment','paid_by','received_by','description','categories',
            ]);

            // ---------- PAYMENTS (OUT) ----------
            $payQ = OfficeTransaction::query();
            // IMPORTANT: your model has a status field; most of your dataset uses 'active'
            $payQ->where('status', 'active');

            if ($from) $payQ->whereDate('date', '>=', $from);
            if ($to)   $payQ->whereDate('date', '<=', $to);
            if ($cat)  $payQ->where('categories', $cat);

            $payments = $payQ->get([
                'id','date','amount','mode_of_payment','paid_by','description','categories',
            ]);

            // ---------- Helpers ----------
            $num = function ($v) {
                if ($v === null || $v === '') return 0.0;
                $s = str_replace(['₹', ',', ' '], '', (string)$v);
                $f = floatval($s);
                return is_finite($f) ? round($f, 2) : 0.0;
            };
            $d8 = fn($d) => $d ? Carbon::parse($d)->format('Y-m-d') : null;

            // ---------- Categories list (ensure at least one if there is data) ----------
            $catFromFunds = collect($funds)->pluck('categories');
            $catFromPays  = collect($payments)->pluck('categories');

            $categories = $catFromFunds
                ->merge($catFromPays)
                ->map(fn($c) => $c ?: 'uncategorized')
                ->unique()
                ->values()
                ->all();

            // If there are rows but all categories were null, we still want one bucket
            if (empty($categories) && ($funds->count() > 0 || $payments->count() > 0)) {
                $categories = ['uncategorized'];
            }

            // ---------- Build category groups ----------
            $groups = [];
            foreach ($categories as $cKey) {
                $groups[$cKey] = [
                    'label'          => $cKey ?: 'uncategorized',
                    'received'       => [],   // funds
                    'paid'           => [],   // payments
                    'received_total' => 0.0,
                    'paid_total'     => 0.0,
                    'net'            => 0.0,
                ];
            }

            // Fill received (Funds)
            foreach ($funds as $f) {
                $ck = $f->categories ?: 'uncategorized';
                // in case there were no categories earlier, initialize now
                if (!isset($groups[$ck])) {
                    $groups[$ck] = [
                        'label'          => $ck,
                        'received'       => [],
                        'paid'           => [],
                        'received_total' => 0.0,
                        'paid_total'     => 0.0,
                        'net'            => 0.0,
                    ];
                    $categories[] = $ck;
                }
                $row = [
                    'id'          => $f->id,
                    'date'        => $d8($f->date),
                    'amount'      => $num($f->amount),
                    'mode'        => $f->mode_of_payment ?? null,
                    'paid_by'     => $f->paid_by ?? null,     // who gave the fund
                    'received_by' => $f->received_by ?? null, // internal receiver
                    'description' => $f->description ?? '',
                    'source'      => 'fund',
                ];
                $groups[$ck]['received'][] = $row;
                $groups[$ck]['received_total'] += $row['amount'];
            }

            // Fill paid (Payments)
            foreach ($payments as $p) {
                $ck = $p->categories ?: 'uncategorized';
                if (!isset($groups[$ck])) {
                    $groups[$ck] = [
                        'label'          => $ck,
                        'received'       => [],
                        'paid'           => [],
                        'received_total' => 0.0,
                        'paid_total'     => 0.0,
                        'net'            => 0.0,
                    ];
                    $categories[] = $ck;
                }
                $row = [
                    'id'          => $p->id,
                    'date'        => $d8($p->date),
                    'amount'      => $num($p->amount),
                    'mode'        => $p->mode_of_payment ?? null,
                    'paid_by'     => $p->paid_by ?? null,   // payer for the expense
                    'received_by' => null,
                    'description' => $p->description ?? '',
                    'source'      => 'payment',
                ];
                $groups[$ck]['paid'][] = $row;
                $groups[$ck]['paid_total'] += $row['amount'];
            }

            // ---------- Finalize + totals ----------
            $sorter = function ($a, $b) {
                if ($a['date'] === $b['date']) return $b['id'] <=> $a['id'];
                return strcmp($b['date'], $a['date']); // desc
            };

            $inGrand = 0.0;
            $outGrand = 0.0;

            foreach ($groups as $ck => $g) {
                usort($groups[$ck]['received'], $sorter);
                usort($groups[$ck]['paid'], $sorter);
                $groups[$ck]['received_total'] = round($groups[$ck]['received_total'], 2);
                $groups[$ck]['paid_total']     = round($groups[$ck]['paid_total'], 2);
                $groups[$ck]['net']            = round($groups[$ck]['received_total'] - $groups[$ck]['paid_total'], 2);

                $inGrand  += $groups[$ck]['received_total'];
                $outGrand += $groups[$ck]['paid_total'];
            }

            // Flat ledger (optional for export)
            $ledger = [];
            $sl = 1;
            foreach ($groups as $ck => $g) {
                foreach ($g['received'] as $r) {
                    $ledger[] = [
                        'sl' => $sl++,
                        'date' => $r['date'],
                        'category' => $ck,
                        'direction' => 'in',
                        'amount' => $r['amount'],
                        'mode' => $r['mode'],
                        'paid_by' => $r['paid_by'],
                        'received_by' => $r['received_by'],
                        'description' => $r['description'],
                        'source' => 'fund',
                        'source_id' => $r['id'],
                    ];
                }
                foreach ($g['paid'] as $r) {
                    $ledger[] = [
                        'sl' => $sl++,
                        'date' => $r['date'],
                        'category' => $ck,
                        'direction' => 'out',
                        'amount' => $r['amount'],
                        'mode' => $r['mode'],
                        'paid_by' => $r['paid_by'],
                        'received_by' => null,
                        'description' => $r['description'],
                        'source' => 'payment',
                        'source_id' => $r['id'],
                    ];
                }
            }

            usort($ledger, function ($a, $b) {
                if ($a['date'] === $b['date']) return $b['source_id'] <=> $a['source_id'];
                return strcmp($b['date'], $a['date']);
            });
            foreach ($ledger as $i => &$r) $r['sl'] = $i + 1;

            // Make sure categories is a clean unique list (preserve order)
            $categories = array_values(array_unique($categories));

            return response()->json([
                'success'     => true,
                'in_total'    => round($inGrand, 2),
                'out_total'   => round($outGrand, 2),
                'net_total'   => round($inGrand - $outGrand, 2),
                'categories'  => $categories,
                'groups'      => $groups,
                'ledger'      => $ledger,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }
}
