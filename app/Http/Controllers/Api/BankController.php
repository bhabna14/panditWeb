<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\BankDetail;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function saveBankDetails(Request $request)
    {
        // Get the active profile
        $panditId = Auth::guard('sanctum')->user()->pandit_id;


        // Validate the request data
        $request->validate([
            'bankname' => 'required|string|max:255',
            'branchname' => 'required|string|max:255',
            'ifsccode' => 'required|string|size:11', // Changed size to 11, usually the length for IFSC codes
            'accname' => 'required|string|max:255',
            'accnumber' => 'required|digits:12',
            'upi_number' => 'required|string|max:255',
        ]);

        // Update or create the bank details
        $bankdata = BankDetail::updateOrCreate(
            ['pandit_id' => $panditId],
            $request->all()
        );

        // Return a JSON response
        return response()->json([
            'success' => 'Bank details saved successfully!', 
            'data' => $bankdata
        ], 200);
    }
}