<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeTransaction;
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

}
