<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\PanditLogin;
use App\Models\PanditDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanditLoginController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $otp = rand(100000, 999999);
        $mobile_no = $request->phone;
        $shortToken = Str::random(6); // WhatsApp button value limit: 15 characters

        // Check if mobile number exists
        $pandit = PanditLogin::where('mobile_no', $mobile_no)->first();

        if ($pandit) {
            // ✅ Existing user: update OTP
            $pandit->otp = $otp;
            $pandit->save();
            $userStatus = 'existing';
        } else {
            // ✅ New user: create record with unique pandit_id
            $pandit = PanditLogin::create([
                'mobile_no' => $mobile_no,
                'otp' => $otp,
                'pandit_id' => 'PANDIT' . rand(10000, 99999),
            ]);
            $userStatus = 'new';
        }

        // MSG91 WhatsApp template payload - corrected
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "to" => $mobile_no,
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
                'token' => $shortToken,
                'user_status' => $userStatus,
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

        // Check if Pandit exists
        $pandit = PanditLogin::where('mobile_no', $request->phoneNumber)->first();

        // If not, create new Pandit record with unique pandit_id
        if (!$pandit) {
            $pandit = PanditLogin::create([
                'mobile_no' => $request->phoneNumber,
                'otp' => $request->otp,
                'pandit_id' => 'PANDIT' . rand(10000, 99999),
            ]);
        }

        // Verify OTP
        if ($pandit->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP or mobile number.'
            ], 401);
        }

        // OTP is valid — clear it
        $pandit->otp = null;
        $pandit->save();

        // Generate Sanctum token
        $token = $pandit->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Pandit authenticated successfully.',
            'token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function panditLogout(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. No user found.',
                ], 401);
            }

            // Revoke the current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Logout successful.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
