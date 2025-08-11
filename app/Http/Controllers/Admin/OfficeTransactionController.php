<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeTransaction;
use App\Models\OfficeFund;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeTransactionController extends Controller
{

    public function getOfficeTransaction()
    {
        return view('admin.office-transaction-details'); // your blade file
    }

    public function saveOfficeTransaction(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:cash,upi',
            'paid_by'         => 'required|string|in:pankaj,subrat,basudha',
            'description'     => 'nullable|string|max:500',
        ]);

        // Save transaction
        OfficeTransaction::create($validatedData);

        // Redirect with success message
        return redirect()->back()->with('success', 'Office transaction saved successfully.');
    }

     public function manageOfficeTransaction()
    {
        // Initial table data (active + latest first)
        $transactions = OfficeTransaction::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        // Today total (status = active)
        $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'));
        $todayTotal = OfficeTransaction::where('status', 'active')
            ->whereDate('date', $today->toDateString())
            ->sum('amount');

        // Optional: initial range total (set 0; will update via AJAX)
        $rangeTotal = 0;

        return view('admin.manage-office-transaction', compact('transactions', 'todayTotal', 'rangeTotal'));
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

        // Shape a compact payload for the table
        $rows = $transactions->map(function ($t, $i) {
            return [
                'sl'             => $i + 1,
                'date'           => Carbon::parse($t->date)->format('Y-m-d'),
                'categories'     => $t->categories,
                'amount'         => number_format((float)$t->amount, 2),
                'mode_of_payment'=> ucfirst($t->mode_of_payment),
                'paid_by'        => ucfirst($t->paid_by),
                'description'    => $t->description,
                'id'             => $t->id,
            ];
        });

        return response()->json([
            'success'      => true,
            'today_total'  => (float) $todayTotal,
            'range_total'  => (float) $rangeTotal,
            'transactions' => $rows,
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

        return redirect()->route('manageOfficePayments')
            ->with('success', 'Office transaction updated successfully.');
    }

    public function destroy($id)
    {
        $transaction = OfficeTransaction::findOrFail($id);

        // Soft delete style (fits your "status = active" listing)
        if ($transaction->isFillable('status')) {
            $transaction->update(['status' => 'deleted']);
        } else {
            // Fallback hard delete if you don’t have a status column
            $transaction->delete();
        }

        return redirect()->route('manageOfficePayments')
            ->with('success', 'Office transaction deleted successfully.');
    }

    public function fundTotalsByCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:rent,rider_salary,vendor_payment,fuel,package,bus_fare,miscellaneous',
        ]);

        $category = $request->query('category');

        $total = OfficeFund::where('categories', $category)->sum('amount');

        // Recent 5 receipts for that category (customize columns as you like)
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

     public function saveOfficeFund(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'date'            => 'required|date',
            'categories'      => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:cash,upi',
            'paid_by'         => 'required|string|in:pankaj,subrat,basudha',
            'received_by'     => 'nullable',
            'description'     => 'nullable|string|max:500',
        ]);

        // Save transaction
        OfficeFund::create($validatedData);

        // Redirect with success message
        return redirect()->back()->with('success', 'Office transaction saved successfully.');
    }

     public function manageOfficeFund()
    {
        // Fetch all office transactions
        $transactions = OfficeFund::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        // Return view with transactions
        return view('admin.manage-office-fund', compact('transactions'));
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

        return redirect()->route('manageOfficeFund')
            ->with('success', 'Office transaction updated successfully.');
    }

    public function destroyOfficeFund($id)
    {
        $transaction = OfficeFund::findOrFail($id);

        // Soft delete style (fits your "status = active" listing)
        if ($transaction->isFillable('status')) {
            $transaction->update(['status' => 'deleted']);
        } else {
            // Fallback hard delete if you don’t have a status column
            $transaction->delete();
        }

        return redirect()->route('manageOfficeFund')
            ->with('success', 'Office transaction deleted successfully.');
    }

}
