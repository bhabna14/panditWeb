<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeTransaction;
use App\Models\OfficeFund;
use App\Models\OfficeLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfficeTransactionController extends Controller
{

    public function getOfficeTransaction()
    {
        return view('admin.office-transaction-details'); // your blade file
    }

    public function manageOfficeTransaction()
    {
        $tz = config('app.timezone', 'Asia/Kolkata');

        $transactions = OfficeTransaction::query()
            ->where('status', 'active')
            ->orderByDesc('date')
            ->get(['id','date','categories','amount','mode_of_payment','paid_by','description']);

        $rangeTotal = (float) OfficeTransaction::where('status','active')->sum('amount');

        $today = Carbon::today($tz)->toDateString();
        $todayTotal = (float) OfficeTransaction::where('status','active')
            ->whereDate('date', $today)
            ->sum('amount');

        $ledgerInTotal  = 0.0;
        $ledgerOutTotal = 0.0;
        $ledgerNetTotal = 0.0;

        return view('admin.manage-office-transaction', compact(
            'transactions',
            'todayTotal',
            'rangeTotal',
            'ledgerInTotal',
            'ledgerOutTotal',
            'ledgerNetTotal'
        ));
    }

    public function filter(Request $request)
    {
        try {
            $v = Validator::make($request->query(), [
                'from_date' => ['nullable','date_format:Y-m-d'],
                'to_date'   => ['nullable','date_format:Y-m-d'],
                'category'  => ['nullable','string','in:rent,rider_salary,vendor_payment,fuel,package,bus_fare,miscellaneous'],
            ], [
                'from_date.date_format' => 'from_date must be in Y-m-d format.',
                'to_date.date_format'   => 'to_date must be in Y-m-d format.',
            ]);

            if ($v->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $v->errors()->first(),
                ], 422);
            }

            $from = $request->query('from_date');
            $to   = $request->query('to_date');
            $cat  = $request->query('category');

            if ($from && $to && $from > $to) {
                return response()->json([
                    'success' => false,
                    'message' => 'From date cannot be after To date.',
                ], 422);
            }

            $q = OfficeTransaction::query()->where('status', 'active');
            if ($from) $q->whereDate('date', '>=', $from);
            if ($to)   $q->whereDate('date', '<=', $to);
            if ($cat)  $q->where('categories', $cat);

            $transactions = $q->orderByDesc('date')->get([
                'id','date','categories','amount','mode_of_payment','paid_by','description'
            ]);

            $rangeTotal = (float) $transactions->sum('amount');

            $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'))->toDateString();
            $todayTotal = (float) OfficeTransaction::where('status','active')
                ->whereDate('date', $today)
                ->sum('amount');

            $list = $transactions->map(function ($t) {
                return [
                    'id'              => $t->id,
                    'date'            => $t->date instanceof Carbon ? $t->date->format('Y-m-d') : Carbon::parse($t->date)->format('Y-m-d'),
                    'categories'      => (string) $t->categories,
                    'amount'          => (float) $t->amount,
                    'mode_of_payment' => (string) ($t->mode_of_payment ?? ''),
                    'paid_by'         => (string) ($t->paid_by ?? ''),
                    'description'     => (string) ($t->description ?? ''),
                ];
            })->values();

            return response()->json([
                'success'      => true,
                'today_total'  => round($todayTotal, 2),
                'range_total'  => round($rangeTotal, 2),
                'transactions' => $list,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }
  public function manageOfficeFund()
    {
        $transactions = OfficeFund::query()
            ->active()
            ->orderBy('date', 'desc')
            ->get();

        $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'))->toDateString();

        $todayTotal = OfficeFund::query()
            ->active()
            ->whereDate('date', $today)
            ->sum('amount');

        $rangeTotal = OfficeFund::query()
            ->active()
            ->sum('amount');

        return view('admin.manage-office-fund', compact('transactions', 'todayTotal', 'rangeTotal'));
    }

    public function filterOfficeFund(Request $request)
    {
        // Always return JSON (avoid redirect/HTML on errors)
        $v = Validator::make($request->query(), [
            'from_date' => ['nullable','date_format:Y-m-d'],
            'to_date'   => ['nullable','date_format:Y-m-d','after_or_equal:from_date'],
        ], [
            'from_date.date_format' => 'from_date must be in Y-m-d format.',
            'to_date.date_format'   => 'to_date must be in Y-m-d format.',
            'to_date.after_or_equal'=> 'to_date must be same or after from_date.',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => $v->errors()->first(),
            ], 422);
        }

        $from = $request->query('from_date');
        $to   = $request->query('to_date');

        $base = OfficeFund::query()->active();
        $query = clone $base;

        // Inclusive range; works for DATE/DATETIME columns
        if ($from && $to) {
            $query->whereDate('date', '>=', $from)
                  ->whereDate('date', '<=', $to);
        } elseif ($from) {
            $query->whereDate('date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date', '<=', $to);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $rangeTotal = (clone $query)->sum('amount');

        $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'))->toDateString();
        $todayTotal = (clone $base)->whereDate('date', $today)->sum('amount');

        $rows = $transactions->map(function ($t, $idx) {
            return [
                'sl'              => $idx + 1,
                'date'            => Carbon::parse($t->date)->format('Y-m-d'),
                'categories'      => (string) $t->categories,
                'amount'          => (float)  $t->amount,          // raw number (no commas)
                'mode_of_payment' => (string) ($t->mode_of_payment ?? ''),
                'paid_by'         => (string) ($t->paid_by ?? ''),
                'received_by'     => (string) ($t->received_by ?? ''),
                'description'     => (string) ($t->description ?? ''),
                'id'              => (int)    $t->id,
            ];
        });

        return response()->json([
            'success'      => true,
            'range_total'  => (float) $rangeTotal,
            'today_total'  => (float) $todayTotal,
            'transactions' => $rows,
        ]);
    }


    public function saveOfficeTransaction(Request $request)
    {
        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:cash,upi',
            'paid_by'         => 'required|string|in:pankaj,subrat,basudha',
            'description'     => 'nullable|string|max:500',
        ]);

        $tx = OfficeTransaction::create($validatedData);

        // LEDGER: add/update corresponding OUT entry
        $this->upsertLedgerFromTransaction($tx);

        return redirect()->back()->with('success', 'Office transaction saved successfully.');
    }

    public function saveOfficeFund(Request $request)
    {
        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string',
            'paid_by'         => 'required|string',
            'received_by'     => 'nullable|string|max:255', // keep nullable to match your form
            'description'     => 'nullable|string|max:500',
        ]);

        $fund = OfficeFund::create($validatedData);

        // LEDGER: add/update corresponding IN entry
        $this->upsertLedgerFromFund($fund);

        return redirect()->back()->with('success', 'Office fund saved successfully.');
    }

    public function filterOfficeTransactions(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $from = $request->query('from_date');
        $to   = $request->query('to_date');

        $query = OfficeTransaction::where('status', 'active');

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date', '<=', $to);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        // Range total
        $rangeTotal = (clone $query)->sum('amount');

        // Today total (independent of filter)
        $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'))->toDateString();
        $todayTotal = OfficeTransaction::where('status', 'active')
            ->whereDate('date', $today)
            ->sum('amount');

        $rows = $transactions->map(function ($t, $i) {
            return [
                'sl'              => $i + 1,
                'date'            => Carbon::parse($t->date)->format('Y-m-d'),
                'categories'      => $t->categories,
                'amount'          => number_format((float)$t->amount, 2),
                'mode_of_payment' => ucfirst($t->mode_of_payment),
                'paid_by'         => ucfirst($t->paid_by),
                'description'     => $t->description,
                'id'              => $t->id,
            ];
        });

        return response()->json([
            'success'      => true,
            'today_total'  => (float) $todayTotal,
            'range_total'  => (float) $rangeTotal,
            'transactions' => $rows,
        ]);
    }

    public function filterOfficeLedger(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
            'category'  => ['nullable', 'string', 'max:255'],
        ]);

        $from = $request->query('from_date');
        $to   = $request->query('to_date');
        $cat  = $request->query('category');

        $q = OfficeLedger::query()
            ->when(OfficeLedger::query()->getModel()->isFillable('status'), fn($qq) => $qq->where('status', 'active'))
            ->when($from && $to, fn($qq) => $qq->whereBetween('entry_date', [$from, $to]))
            ->when($from && !$to, fn($qq) => $qq->whereDate('entry_date', '>=', $from))
            ->when(!$from && $to, fn($qq) => $qq->whereDate('entry_date', '<=', $to))
            ->when($cat, fn($qq) => $qq->where('category', $cat))
            ->orderBy('entry_date', 'desc')->orderBy('id', 'desc');

        $rows = $q->get();

        $inTotal  = (clone $q)->where('direction', 'in')->sum('amount');
        $outTotal = (clone $q)->where('direction', 'out')->sum('amount');

        $data = $rows->map(function ($r, $i) {
            return [
                'sl'           => $i + 1,
                'date'         => Carbon::parse($r->entry_date)->format('Y-m-d'),
                'category'     => $r->category,
                'direction'    => $r->direction, // in / out
                'amount'       => number_format((float)$r->amount, 2),
                'mode'         => ucfirst((string)$r->mode_of_payment),
                'paid_by'      => ucfirst((string)$r->paid_by),
                'received_by'  => (string)($r->received_by ?? ''),
                'description'  => (string)$r->description,
                'source'       => $r->source_type, // fund/transaction
                'source_id'    => $r->source_id,
            ];
        });

        return response()->json([
            'success'    => true,
            'in_total'   => (float) $inTotal,
            'out_total'  => (float) $outTotal,
            'net_total'  => (float) $inTotal - (float) $outTotal,
            'ledger'     => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $transaction = OfficeTransaction::findOrFail($id);

        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:cash,upi',
            'paid_by'         => 'required|string|in:pankaj,subrat,basudha',
            'description'     => 'nullable|string|max:500',
        ]);

        $transaction->update($validatedData);

        // LEDGER: sync OUT entry
        $this->upsertLedgerFromTransaction($transaction);

        return redirect()->route('manageOfficePayments')
            ->with('success', 'Office transaction updated successfully.');
    }

    public function updateOfficeFund(Request $request, $id)
    {
        $transaction = OfficeFund::findOrFail($id);

        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:cash,upi',
            'paid_by'         => 'required|string|in:pankaj,subrat,basudha',
            'received_by'     => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:500',
        ]);

        $transaction->update($validatedData);

        // LEDGER: sync IN entry
        $this->upsertLedgerFromFund($transaction);

        return redirect()->route('manageOfficeFund')
            ->with('success', 'Office fund updated successfully.');
    }

    public function destroy($id)
    {
        $transaction = OfficeTransaction::findOrFail($id);

        if ($transaction->isFillable('status')) {
            $transaction->update(['status' => 'deleted']);
        } else {
            $transaction->delete();
        }

        // LEDGER: soft delete OUT entry
        $this->markLedgerDeleted('transaction', $transaction->id);

        return redirect()->route('manageOfficePayments')
            ->with('success', 'Office transaction deleted successfully.');
    }

    public function destroyOfficeFund($id)
    {
        $transaction = OfficeFund::findOrFail($id);

        if ($transaction->isFillable('status')) {
            $transaction->update(['status' => 'deleted']);
        } else {
            $transaction->delete();
        }

        // LEDGER: soft delete IN entry
        $this->markLedgerDeleted('fund', $transaction->id);

        return redirect()->route('manageOfficeFund')->with('success', 'Office fund deleted successfully.');
    }

    public function fundTotalsByCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:rent,rider_salary,vendor_payment,fuel,package,bus_fare,miscellaneous',
        ]);

        $category = $request->query('category');

        $total = OfficeFund::where('categories', $category)->sum('amount');

        $items = OfficeFund::select('date', 'amount', 'mode_of_payment', 'paid_by', 'received_by', 'description')
            ->where('categories', $category)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success'        => true,
            'category'       => $category,
            'total_received' => (float) $total,
            'count'          => $items->count(),
            'items'          => $items,
        ]);
    }

    private function upsertLedgerFromFund(OfficeFund $fund): void
    {
        OfficeLedger::updateOrCreate(
            ['source_type' => 'fund', 'source_id' => $fund->id],
            [
                'entry_date'      => $fund->date,
                'category'        => $fund->categories,
                'direction'       => 'in',
                'amount'          => $fund->amount,
                'mode_of_payment' => $fund->mode_of_payment,
                'paid_by'         => $fund->paid_by,
                'received_by'     => $fund->received_by,
                'description'     => $fund->description,
                'status'          => method_exists($fund, 'getAttribute') && $fund->getAttribute('status')
                                        ? $fund->status : 'active',
            ]
        );
    }

    private function upsertLedgerFromTransaction(OfficeTransaction $tx): void
    {
        OfficeLedger::updateOrCreate(
            ['source_type' => 'transaction', 'source_id' => $tx->id],
            [
                'entry_date'      => $tx->date,
                'category'        => $tx->categories,
                'direction'       => 'out',
                'amount'          => $tx->amount,
                'mode_of_payment' => $tx->mode_of_payment,
                'paid_by'         => $tx->paid_by,
                'received_by'     => null,
                'description'     => $tx->description,
                'status'          => method_exists($tx, 'getAttribute') && $tx->getAttribute('status')
                                        ? $tx->status : 'active',
            ]
        );
    }

    private function markLedgerDeleted(string $sourceType, int $sourceId): void
    {
        OfficeLedger::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->update(['status' => 'deleted']);
    }
}
