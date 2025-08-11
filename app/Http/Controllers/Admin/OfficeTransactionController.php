<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeTransaction;
use App\Models\OfficeFund;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeTransactionController extends Controller
{

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
        // Fetch all office transactions
        $transactions = OfficeTransaction::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        // Return view with transactions
        return view('admin.manage-office-transaction', compact('transactions'));
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
