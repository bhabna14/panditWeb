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

            // Generate a new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } else {
            // OTP is invalid, return error response
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }
    }
}
