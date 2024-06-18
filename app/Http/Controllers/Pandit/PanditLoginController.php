<?php

namespace App\Http\Controllers\pandit;

use App\Models\PanditLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PanditLoginController extends Controller
{
    public function showOtpForm()
    {
        return view('/pandit/panditotp');
    }

    public function storeLoginData(Request $request)
    {
        $data = $request->validate([
            'otp' => 'integer',
            'pandit_id' => 'required|string',
            'mobile_no' => 'required|string',
        ]);
    
        // Attempt to save the OTP data
        if (PanditLogin::create($data)) {
            return redirect()->route('pandit.otp')->with('success', 'OTP generated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to save OTP.');
        }
    }
    
   
public function checkOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|integer',
    ]);
    

    $inputOtp = $request->input('otp');
    $user = PanditLogin::where('otp', $inputOtp)->first();

    // Check if user exists and the OTP matches
    if ($user) {
        // Log the user in
        Auth::guard('pandits')->login($user);
        // Clear the OTP after successful validation
        $user->otp = null;
        $user->save();

        return redirect()->route('pandit.profile')->with('success', 'Login successful.');
    } else {
        // OTP is invalid, redirect back with an error message
        return redirect()->route('pandit.otp')->with('error', 'Invalid OTP.');
    }
}
    
}
