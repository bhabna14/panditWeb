<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class OtpController extends Controller
{

    public function userLogout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'No authenticated user found.'], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'User logged out successfully.'
        ], 200);
    }

    public function loginWithMobile(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->input('mobile_number');
        $otp = $request->input('otp');

        // Static OTP check
        if ($otp !== '000000') {
            return response()->json([
                'message' => 'Invalid OTP.'
            ], 401);
        }

        // Find user by mobile number
        $user = User::where('mobile_number', $phoneNumber)->first();

        if ($user) {
            // Optional: Update any fields if needed
            $user->update([
                'mobile_number' => $phoneNumber, // redundant but you can add more fields to update
            ]);
        } else {
            // Create new user
            $user = User::create([
                'userid' => 'USER' . rand(10000, 99999),
                'mobile_number' => $phoneNumber,
            ]);
        }

        // Create token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'User authenticated successfully.',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $otp = rand(100000, 999999);
        $phone = $request->phone;
        $shortToken = Str::random(6); // max 15 characters

        // Check if pandit already exists
        $pandit = User::where('mobile_number', $phone)->first();

        if ($pandit) {
            // ✅ Existing: update OTP
            $pandit->otp = $otp;
            $pandit->save();
            $status = 'existing';
        } else {
            // ✅ New: create with new pandit_id
            $pandit = User::create([
                'mobile_number' => $phone,
                'otp' => $otp,
                'userid' => 'USER' . rand(10000, 99999)
            ]);
            $status = 'new';
        }

        // ✅ Corrected MSG91 WhatsApp template payload
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "to" => $phone, // ✅ must be here
                "type" => "template",
                "template" => [
                    "name" => env('MSG91_WA_TEMPLATE'),
                    "language" => [
                        "code" => "en",
                        "policy" => "deterministic"
                    ],
                    "namespace" => env('MSG91_WA_NAMESPACE'),
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string) $otp
                                ]
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => 0,
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $shortToken
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'authkey' => env('MSG91_AUTHKEY'),
            ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/', $payload);

            $result = $response->json();

            if ($response->status() === 401 || ($result['status'] ?? '') === 'fail') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Check MSG91 credentials or template settings.',
                    'error' => $result
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'user_status' => $status,
                'token' => $shortToken,
                'api_response' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|string',
            'otp' => 'required|string'
        ]);

        // Find user by phone number
        $user = User::where('mobile_number', $request->phoneNumber)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Mobile number not found. Please request OTP first.'
            ], 404);
        }

        // Check OTP match
        if ($user->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP.'
            ], 401);
        }

        // OTP is valid — clear it
        $user->otp = null;
        $user->save();

        // Generate Sanctum token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'User authenticated successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

}