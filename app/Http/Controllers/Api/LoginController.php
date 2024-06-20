<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PanditLogin;


class LoginController extends Controller
{

  public function storeLoginData(Request $request)
{
    // Validate the input data including mobile number format
    $data = $request->validate([
        'mobile_no' => 'required|string|regex:/^\d{10}$/',
    ], [
        'mobile_no.regex' => 'Mobile number must be exactly 10 digits.',
    ]);

    // Retrieve the user by mobile number
    $user = PanditLogin::where('mobile_no', $data['mobile_no'])->first();

    // Generate a new OTP and pandit_id
    $otp = rand(1000, 9999);
    $panditId = 'PANDIT' . rand(10000, 99999);

    if ($user) {
        // Update the existing user's OTP and pandit_id
        $user->otp = $otp;
        $user->pandit_id = $panditId;

        // Save the user and handle potential save errors
        if ($user->save()) {
            Auth::guard('pandits')->login($user);
            return response()->json([
                'success' => true,
                'message' => 'OTP updated successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update OTP.',
            ], 500);
        }
    } else {
        // Create a new user with the provided data, OTP, and pandit_id
        $data['otp'] = $otp;
        $data['pandit_id'] = $panditId;

        $user = PanditLogin::create($data);

        // Save the user and handle potential save errors
        if ($user) {
            Auth::guard('pandits')->login($user);
            return response()->json([
                'success' => true,
                'message' => 'OTP generated successfully.',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save OTP.',
            ], 500);
        }
    }
}

    public function checkOtp(Request $request)
{
    $data = $request->validate([
        'otp' => 'required|integer',
    ]);

    $user = Auth::guard('pandits')->user();

    // Check if the user is authenticated
    if (!$user) {
        return response()->json(['error' => 'You must be logged in to verify OTP.'], 401);
    }

    $panditId = $user->pandit_id;
    $inputOtp = $data['otp'];

    // Check if the pandit_id exists in the Profile model
    $profile = Profile::where('pandit_id', $panditId)->first();

    if (!$profile) {
        if ($user->otp == $inputOtp) {
            // Clear the OTP after successful validation
            $user->otp = null;
            $user->save();

            // Return success response
            return response()->json(['message' => 'Login successful. Redirecting to profile.'], 200);
        } else {
            // OTP is invalid, return error response
            return response()->json(['error' => 'Invalid OTP.'], 400);
        }
    }

    // If profile exists, validate OTP
    if ($user->otp == $inputOtp) {
        // Clear the OTP after successful validation
        $user->otp = null;
        $user->save();

        // Return success response
        return response()->json(['message' => 'Login successful. Redirecting to dashboard.'], 200);
    } else {
        // OTP is invalid, return error response
        return response()->json(['error' => 'Invalid OTP.'], 400);
    }
}
    
}
