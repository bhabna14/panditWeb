<?php

namespace App\Http\Controllers\pandit;

use App\Models\PanditLogin;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PanditLoginController extends Controller
{
    
    public function storeLoginData(Request $request)
    {
        // Validate the input data
        $data = $request->validate([
            'mobile_no' => 'required|string|regex:/^\d{10}$/',
        ], [
            'mobile_no.regex' => 'Mobile number must be exactly 10 digits.',
        ]);
    
        // Retrieve the user by mobile number
        $user = PanditLogin::where('mobile_no', $data['mobile_no'])->first();
    
        // Generate a new OTP
        $otp = rand(1000, 9999);
    
        if ($user) {
            // Check if the user already has a pandit_id
            if (empty($user->pandit_id)) {
                // Generate a new pandit_id if it does not exist
                $panditId = 'PANDIT' . rand(10000, 99999);
                $user->pandit_id = $panditId;
            }
    
            // Update the existing user's OTP
            $user->otp = $otp;
    
            // Save the user and handle potential save errors
            if ($user->save()) {
                Auth::guard('pandits')->login($user);
                return redirect()->route('pandit.otp')->with('success', 'OTP updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to update OTP.');
            }
        } else {
            // Generate a new pandit_id for a new user
            $panditId = 'PANDIT' . rand(10000, 99999);
    
            // Create a new user with the provided data, OTP, and pandit_id
            $data['otp'] = $otp;
            $data['pandit_id'] = $panditId;
    
            $user = PanditLogin::create($data);
    
            // Save the user and handle potential save errors
            if ($user) {
                Auth::guard('pandits')->login($user);
                return redirect()->route('pandit.otp')->with('success', 'OTP generated successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to save OTP.');
            }
        }
    }
    
    
        public function checkOtp(Request $request)
        {
            $request->validate([
                'otp' => 'required|integer',
            ]);
        
            $user = Auth::guard('pandits')->user();
        
            // Check if the user is authenticated
            if (!$user) {
                return redirect()->route('pandit.login')->with('error', 'You must be logged in to verify OTP.');
            }
        
            $panditId = $user->pandit_id;
            $inputOtp = $request->input('otp');
        
            // Check if the pandit_id exists in the Profile model
            $profile = Profile::where('pandit_id', $panditId)->first();
        
            if (!$profile) {
                if ($user->otp == $inputOtp) {
                    // Clear the OTP after successful validation
                    $user->otp = null;
                    $user->save();

                    return redirect()->route('pandit.profile')->with('success', 'Login successful.');
                } else {
                    // OTP is invalid, redirect back with an error message
                    return redirect()->route('pandit.otp')->with('error', 'Invalid OTP.');
                }
               
            }

            // If profile exists, validate OTP
            if ($user->otp == $inputOtp) {
                // Clear the OTP after successful validation
                $user->otp = null;
                $user->save();
        
                // Redirect to dashboard
                return redirect()->route('pandit.dashboard')->with('success', 'Login successful.');
            } else {
                // OTP is invalid, redirect back with an error message
                return redirect()->route('pandit.otp')->with('error', 'Invalid OTP.');
            }
        }
        
    
        public function showOtpForm()
        {
            return view('pandit/panditotp');
        }
public function showLoginForm(){
    return view('pandit/demo-login');

}
}
