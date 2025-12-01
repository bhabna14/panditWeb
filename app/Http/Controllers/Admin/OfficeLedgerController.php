<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfficeLedgerController extends Controller
{
    /** Render the category-first ledger view */
    public function index(Request $request)
    {
        // Distinct categories for the dropdown
        $categories = OfficeLedger::query()
            ->active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        // Make sure this matches your Blade path
        return view('admin.office-ledger-transaction', [
            'categories' => $categories,
        ]);
    }

    /** API: category-grouped ledger the Blade expects */
    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
            'category'  => ['nullable', 'string', 'max:255'],
        ]);

        $from = $request->query('from_date');
        $to   = $request->query('to_date');
        $cat  = $request->query('category');

        // Base query: ACTIVE ledger rows, optional filters
        $q = OfficeLedger::query()
            ->active()
            // ✅ FIX: always use whereDate so the full day is covered
            ->when($from && $to, function ($qq) use ($from, $to) {
                $qq->whereDate('entry_date', '>=', $from)
                   ->whereDate('entry_date', '<=', $to);
            })
            ->when($from && !$to, fn($qq) => $qq->whereDate('entry_date', '>=', $from))
            ->when(!$from && $to, fn($qq) => $qq->whereDate('entry_date', '<=', $to))
            ->when($cat,         fn($qq) => $qq->where('category', $cat));

        $rows = $q->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->get([
                'id',
                'entry_date',
                'category',
                'direction',
                'amount',
                'mode_of_payment',
                'paid_by',
                'received_by',
                'description',
                'source_type',
                'source_id',
            ]);

        // Totals (compute once on filtered set)
        $inTotal  = (float) $rows->where('direction', 'in')->sum('amount');
        $outTotal = (float) $rows->where('direction', 'out')->sum('amount');

        // If absolutely no rows, return a minimal payload (Blade shows message).
        if ($rows->isEmpty()) {
            return response()->json([
                'success'    => true,
                'in_total'   => 0.0,
                'out_total'  => 0.0,
                'net_total'  => 0.0,
                'categories' => [],
                'groups'     => new \stdClass(),
                'ledger'     => [],
            ]);
        }

        // Normalize values
        $rows = $rows->map(function ($r) {
            return [
                'id'          => $r->id,
                'date'        => $r->entry_date ? Carbon::parse($r->entry_date)->format('Y-m-d') : null,
                'category'    => $r->category ?: 'uncategorized',
                'direction'   => $r->direction,
                'amount'      => (float) $r->amount,
                'mode'        => $r->mode_of_payment ?: null,
                'paid_by'     => $r->paid_by ?: null,
                'received_by' => $r->received_by ?: null,
                'description' => $r->description ?: '',
                'source'      => $r->source_type,
                'source_id'   => $r->source_id,
            ];
        });

        $categories = $rows->pluck('category')->unique()->values()->all();

        // Build category groups
        $groups = [];
        foreach ($categories as $cKey) {
            $groups[$cKey] = [
                'label'          => $cKey,
                'received'       => [],
                'paid'           => [],
                'received_total' => 0.0,
                'paid_total'     => 0.0,
                'net'            => 0.0,
            ];
        }

        foreach ($rows as $r) {
            $ck = $r['category'];

            if ($r['direction'] === 'in') {
                $groups[$ck]['received'][] = [
                    'id'          => $r['id'],
                    'date'        => $r['date'],
                    'amount'      => $r['amount'],
                    'mode'        => $r['mode'],
                    'paid_by'     => $r['paid_by'],
                    'received_by' => $r['received_by'],
                    'description' => $r['description'],
                    'source'      => $r['source'],
                ];
                $groups[$ck]['received_total'] += $r['amount'];
            } else {
                $groups[$ck]['paid'][] = [
                    'id'          => $r['id'],
                    'date'        => $r['date'],
                    'amount'      => $r['amount'],
                    'mode'        => $r['mode'],
                    'paid_by'     => $r['paid_by'],
                    'description' => $r['description'],
                    'source'      => $r['source'],
                ];
                $groups[$ck]['paid_total'] += $r['amount'];
            }
        }

        // Sort inner arrays and finalize totals
        foreach ($groups as $ck => $g) {
            usort(
                $groups[$ck]['received'],
                fn($a, $b) => ($b['date'] <=> $a['date']) ?: ($b['id'] <=> $a['id'])
            );
            usort(
                $groups[$ck]['paid'],
                fn($a, $b) => ($b['date'] <=> $a['date']) ?: ($b['id'] <=> $a['id'])
            );

            $groups[$ck]['received_total'] = round($groups[$ck]['received_total'], 2);
            $groups[$ck]['paid_total']     = round($groups[$ck]['paid_total'], 2);
            $groups[$ck]['net']            = round($groups[$ck]['received_total'] - $groups[$ck]['paid_total'], 2);
        }

        // Optional flat ledger (for export, if needed later)
        $flat = [];
        $sl   = 1;

        foreach ($categories as $ck) {
            foreach ($groups[$ck]['received'] as $r) {
                $flat[] = [
                    'sl'          => $sl++,
                    'date'        => $r['date'],
                    'category'    => $ck,
                    'direction'   => 'in',
                    'amount'      => $r['amount'],
                    'mode'        => $r['mode'],
                    'paid_by'     => $r['paid_by'],
                    'received_by' => $r['received_by'],
                    'description' => $r['description'],
                    'source'      => $r['source'],
                ];
            }
            foreach ($groups[$ck]['paid'] as $r) {
                $flat[] = [
                    'sl'          => $sl++,
                    'date'        => $r['date'],
                    'category'    => $ck,
                    'direction'   => 'out',
                    'amount'      => $r['amount'],
                    'mode'        => $r['mode'],
                    'paid_by'     => $r['paid_by'],
                    'received_by' => null,
                    'description' => $r['description'],
                    'source'      => $r['source'],
                ];
            }
        }

        usort($flat, fn($a, $b) => ($b['date'] <=> $a['date']) ?: ($b['sl'] <=> $a['sl']));

        return response()->json([
            'success'    => true,
            'in_total'   => round($inTotal, 2),
            'out_total'  => round($outTotal, 2),
            'net_total'  => round($inTotal - $outTotal, 2),
            'categories' => array_values($categories),
            'groups'     => $groups,
            'ledger'     => $flat,
        ]);
    }
}
