<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    //
    public function storeLoginData(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'phonenumber' => 'required|string|regex:/^\d{10}$/',
        ]);

        // Concatenate country code with phone number
        $phonenumber = '+91'. $data['phonenumber'];
        // dd($phonenumber);
        // Check if a user with this phone number already exists
        $user = User::where('phonenumber', $phonenumber)->first();
        // dd($user);
        $otp = rand(1000, 9999);
        $userid = 'USER' . rand(10000, 99999);
        if ($user) {
            // User exists, update the OTP
            $user->otp = $otp;
        } else {
            // User doesn't exist, create a new one
           
            $user = new User();
            $user->userid =  $userid;
            $user->phonenumber = $phonenumber;
            $user->otp = $otp;
        }

        // Save the user (either update or create)
        if ($user->save()) {
            return response()->json([
                'success' => 200,
                'message' => 'OTP generated successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => 400,
                'message' => 'OTP generated successfully.',
            ], 400);
        }
    }

    public function checkUserOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);

        $inputOtp = $request->input('otp');
        
        $user = User::where('otp', $inputOtp)->first();

        // Check if user exists and the OTP matches
        if ($user) {
            // Log the user in
            Auth::guard('users')->login($user);
            // Clear the OTP after successful validation
            $user->otp = null;
            $user->save();

            return response()->json(['message' => 'Login successful.', 'user' => $user], 200);
        } else {
            // OTP is invalid, return error response
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }
    }
}
