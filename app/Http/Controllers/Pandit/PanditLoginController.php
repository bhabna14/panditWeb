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
            $data = $request->validate([
                'otp' => 'required|integer',
                'pandit_id' => 'required|string',
                'mobile_no' => 'required|string',
            ]);
    
            // Check if the mobile number already exists
            $user = PanditLogin::where('mobile_no', $data['mobile_no'])->first();
    
            if ($user) {
                // Mobile number exists, update OTP only
                $user->otp = $data['otp'];
                if ($user->save()) {
                    // Authenticate the user
                    Auth::guard('pandits')->login($user);
                    return redirect()->route('pandit.otp')->with('success', 'OTP updated successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to update OTP.');
                }
            } else {
                // Mobile number does not exist, create new record
                $user = PanditLogin::create($data);
                if ($user) {
                    // Authenticate the user
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
            
                    // Redirect to dashboard
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

}
