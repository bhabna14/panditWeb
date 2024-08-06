<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Bankdetail;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function saveBankDetails(Request $request)
    {
        // Get the active profile
        $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
        // Validate the request data
        // $request->validate([
        //     'bankname' => 'required|string|max:255',
        //     'branchname' => 'required|string|max:255',
        //     'ifsccode' => 'required|string|size:11', // Changed size to 11, usually the length for IFSC codes
        //     'accname' => 'required|string|max:255',
        //     'accnumber' => 'required|digits:12',
        //     'upi_number' => 'required|string|max:255',
        // ]);
    
        // Update or create the bank details
        $bankdata = BankDetail::updateOrCreate(
            ['pandit_id' => $panditId],
            [
                'bankname' => $request->bankname,
                'branchname' => $request->branchname,
                'ifsccode' => $request->ifsccode,
                'accname' => $request->accname,
                'accnumber' => $request->accnumber,
                'upi_number' => $request->upi_number,
            ]
        );
    
        // Return a JSON response
        return response()->json([
            'success' => 'Bank details saved successfully!', 
            'data' => $bankdata
        ], 200);
    }
    public function getBankDetails()
    {
        // Get the active profile
        $panditId = Auth::guard('sanctum')->user()->pandit_id;

        // Retrieve the bank details for the pandit
        $bankdata = BankDetail::where('pandit_id', $panditId)->first();

        // Check if bank details are found
        if (!$bankdata) {
            return response()->json([
                'success' => true,
                'message' => 'Bank details not found!'
            ], 200);
        }

        // Return a JSON response with the bank details
        return response()->json([
            'success' => 'Bank details retrieved successfully!', 
            'data' => $bankdata
        ], 200);
    }

    
}