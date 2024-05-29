<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Bankdetail;



class BankController extends Controller
{
    //
  
    public function bankdetails()
    {
        $bankdata = Bankdetail::first();
        return view('pandit/panditbank', compact('bankdata'));
    }

    public function savebankdetails(Request $request)
    {
        // $userId = Auth::id();
        $profile = Profile::where('status', 'active')->first();
         $profileId = $profile->profile_id;
        
        $request->validate([
            'bankname' => '|required|string|max:255',
            'branchname' => '|required|string|max:255|',
            'ifsccode' => 'required|string|size:10',
            'accname' => '|required|string|max:255',
            'accnumber' => 'required|digits:12',
            'upi_number' => 'required',
        ]);

        $bankdata = BankDetail::updateOrCreate(
            ['pandit_id' => $profileId],
            $request->all()
        );

        return redirect()->back()->with('success', 'Bank details saved successfully!');
    }
}
