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
use App\Models\UserDevice;
use Illuminate\Support\Facades\DB;


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

        // normalize phone (keeps digits only)
        $phone = preg_replace('/\D+/', '', $request->phone ?? '');
        $otp = random_int(100000, 999999);
        $shortToken = Str::upper(Str::random(6)); // max 15 chars allowed, keep it short

        // Create/update user + referral_code atomically
        try {
            DB::beginTransaction();

            // lock the row if it exists to prevent two parallel requests creating codes at once
            $pandit = User::where('mobile_number', $phone)->lockForUpdate()->first();
            $status = 'existing';

            if ($pandit) {
                // existing user: update OTP, and backfill referral_code if missing
                $pandit->otp = $otp;

                if (empty($pandit->referral_code)) {
                    $pandit->referral_code = $this->generateReferralCode();
                }

                $pandit->save();
            } else {
                // new user: create with unique userid + referral_code
                $pandit = User::create([
                    'mobile_number' => $phone,
                    'otp'           => $otp,
                    'userid'        => $this->generateUserId(),
                    'referral_code' => $this->generateReferralCode(),
                ]);
                $status = 'new';
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare OTP',
                'error'   => $e->getMessage(),
            ], 500);
        }

        // MSG91 WhatsApp template payload (unchanged)
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "to" => $phone,
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
                'authkey'      => env('MSG91_AUTHKEY'),
            ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/', $payload);

            $result = $response->json();

            if ($response->status() === 401 || ($result['status'] ?? '') === 'fail') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Check MSG91 credentials or template settings.',
                    'error'   => $result
                ], 401);
            }

            return response()->json([
                'success'        => true,
                'message'        => 'OTP sent successfully',
                'user_status'    => $status,
                'token'          => $shortToken,
                // expose referral_code for client use if you want
                'referral_code'  => $pandit->referral_code,
                'api_response'   => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|string',
            'otp' => 'required|string',
            'device_id' => 'required|string',
            'platform' => 'required|string',
            'device_model' => 'required|string',
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

        // Store device info
        UserDevice::updateOrCreate(
            ['device_id' => $request->device_id, 'user_id' => $user->userid], // match by device + user
            [
                'platform' => $request->platform,
                'device_model' => $request->device_model,
            ]
        );

        // Generate Sanctum token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'User authenticated successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    private function generateReferralCode(int $length = 7): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $exists = User::where('referral_code', $code)->exists();
        } while ($exists);

        return $code;
    }

    private function generateUserId(): string
    {
        do {
            $id = 'USER' . random_int(10000, 99999);
            $exists = User::where('userid', $id)->exists();
        } while ($exists);

        return $id;
    }

}