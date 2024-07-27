<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Bankdetail;
use Illuminate\Support\Facades\Auth;



class BankController extends Controller
{
    //
  
    public function bankdetails()
    {
    $panditId = Auth::guard('pandits')->user()->pandit_id;

    $bankdata = Bankdetail::where('pandit_id', $panditId)->first();

    if (!$bankdata) {
        $bankdata = new Bankdetail();
        $bankdata->pandit_id = $panditId;
    }

    return view('pandit.panditbank', compact('bankdata'));
    }

    public function savebankdetails(Request $request)
    {
        // Get the authenticated Pandit's pandit_id
        $panditId = Auth::guard('pandits')->user()->pandit_id;
    
        // Validate the incoming request data
        $request->validate([
            'bankname' => 'required|string|max:255',
            'branchname' => 'required|string|max:255',
            'ifsccode' => 'required|size:10',
            'accname' => 'required|string|max:255',
            'accnumber' => 'required|digits_between:10,12',
            'upi_number' => 'required|string|max:255',
        ]);
    
        // Update or create the bank details for the authenticated Pandit
        $bankdata = BankDetail::updateOrCreate(
            ['pandit_id' => $panditId],
            $request->all()
        );
    
        // Redirect back with success message upon successful save
        return redirect()->back()->with('success', 'Bank details saved successfully!');
    }
    
}
