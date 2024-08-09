<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PanditLogin;
use App\Models\Profile;

class LoginController extends Controller
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
        $otp = 1111;
    
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
                return response()->json(['message' => 'OTP updated successfully.', 'otp' => $otp], 200);
            } else {
                return response()->json(['message' => 'Failed to update OTP.'], 500);
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
                return response()->json(['message' => 'OTP generated successfully.', 'otp' => $otp], 200);
            } else {
                return response()->json(['message' => 'Failed to save OTP.'], 500);
            }
        }
    }


   public function checkOtp(Request $request)
{
    // Validate the input data
    $request->validate([
        'otp' => 'required|integer',
    ]);

    // Retrieve the authenticated user
    $user = PanditLogin::where('otp', $request->otp)->first();

    // Check if the user exists and the OTP matches
    if ($user) {
        // Clear the OTP after successful validation
        $user->otp = null;
        $user->save();

        // Generate a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    } else {
        return response()->json(['message' => 'Invalid OTP.'], 401);
    }
}
    
}
