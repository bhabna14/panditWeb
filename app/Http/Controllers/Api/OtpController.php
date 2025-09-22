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
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevice;

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

    // public function sendOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|string',
    //     ]);

    //     $otp = random_int(100000, 999999);
    //     $phone = $request->phone;
    //     $shortToken = Str::random(6); // max 15 characters

    //     // Check if user already exists
    //     $pandit = User::where('mobile_number', $phone)->first();

    //     if ($pandit) {
    //         // ✅ Existing user: update OTP
    //         $pandit->otp = $otp;

    //         // If referral_code is missing, generate one
    //         if (empty($pandit->referral_code)) {
    //             $pandit->referral_code = $this->generateReferralCode();
    //         }

    //         $pandit->save();
    //         $status = 'existing';
    //     } else {
    //         // ✅ New user: create with referral_code
    //         $pandit = User::create([
    //             'mobile_number'  => $phone,
    //             'otp'            => $otp,
    //             'userid'         => 'USER' . random_int(10000, 99999),
    //             'referral_code'  => $this->generateReferralCode(),
    //         ]);
    //         $status = 'new';
    //     }

    //     // ✅ MSG91 WhatsApp template payload
    //     $payload = [
    //         "integrated_number" => env('MSG91_WA_NUMBER'),
    //         "content_type" => "template",
    //         "payload" => [
    //             "messaging_product" => "whatsapp",
    //             "to" => $phone,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => env('MSG91_WA_TEMPLATE'),
    //                 "language" => [
    //                     "code" => "en",
    //                     "policy" => "deterministic"
    //                 ],
    //                 "namespace" => env('MSG91_WA_NAMESPACE'),
    //                 "components" => [
    //                     [
    //                         "type" => "body",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" => (string) $otp
    //                             ]
    //                         ]
    //                     ],
    //                     [
    //                         "type" => "button",
    //                         "sub_type" => "url",
    //                         "index" => 0,
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" => $shortToken
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     try {
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'authkey' => env('MSG91_AUTHKEY'),
    //         ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/', $payload);

    //         $result = $response->json();

    //         if ($response->status() === 401 || ($result['status'] ?? '') === 'fail') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized: Check MSG91 credentials or template settings.',
    //                 'error'   => $result
    //             ], 401);
    //         }

    //         return response()->json([
    //             'success'      => true,
    //             'message'      => 'OTP sent successfully',
    //             'user_status'  => $status,
    //             'token'        => $shortToken,
    //             // (Optional) expose referral_code when applicable
    //             'referral_code'=> $pandit->referral_code,
    //             'api_response' => $result
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to send OTP',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $request->phone;
        $shortToken = Str::random(6); // max 15 characters

        // ✅ Special static OTP case
        if ($phone === '+919876543210') {
            $otp = 123456;
            $skipWhatsApp = true;
        } else {
            $otp = random_int(100000, 999999); // normal flow
            $skipWhatsApp = false;
        }

        // 🔍 Check if user exists
        $pandit = User::where('mobile_number', $phone)->first();

        if ($pandit) {
            // Existing user → update OTP
            $pandit->otp = $otp;

            if (empty($pandit->referral_code)) {
                $pandit->referral_code = $this->generateReferralCode();
            }

            $pandit->save();
            $status = 'existing';
        } else {
            // New user → create
            $pandit = User::create([
                'mobile_number' => $phone,
                'otp'           => $otp,
                'userid'        => 'USER' . random_int(10000, 99999),
                'referral_code' => $this->generateReferralCode(),
            ]);
            $status = 'new';
        }

        // ✅ If test number → skip MSG91 call
        if ($skipWhatsApp) {
            return response()->json([
                'success'       => true,
                'message'       => 'Static OTP generated (test mode).',
                'user_status'   => $status,
                'otp'           => $otp, // Exposed for testing only
                'referral_code' => $pandit->referral_code,
            ], 200);
        }

        // ✅ Otherwise → send via MSG91 WhatsApp
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
                'success'       => true,
                'message'       => 'OTP sent successfully',
                'user_status'   => $status,
                'token'         => $shortToken,
                'referral_code' => $pandit->referral_code,
                'api_response'  => $result
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
            'phoneNumber'  => 'required|string',
            'otp'          => 'required|string',
            'device_id'    => 'required|string',
            'platform'     => 'required|string',
            'device_model' => 'required|string',
        ]);

        $phone = $request->phoneNumber;

        // Find user by phone number
        $user = User::where('mobile_number', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Mobile number not found. Please request OTP first.'
            ], 404);
        }

        // ✅ Special static OTP case
        if ($phone === '+919876543210') {
            if ($request->otp !== '123456') {
                return response()->json(['message' => 'Invalid OTP.'], 401);
            }
        } else {
            // Normal OTP check
            if ((string)$user->otp !== (string)$request->otp) {
                return response()->json(['message' => 'Invalid OTP.'], 401);
            }
        }

        // Ensure referral_code exists
        if (empty($user->referral_code)) {
            $user->referral_code = $this->generateReferralCode();
        }

        // Clear OTP for non-test users
        if ($phone !== '7008515765') {
            $user->otp = null;
        }

        $user->save();

        // Store device info
        UserDevice::updateOrCreate(
            [
                'device_id' => $request->device_id,
                // If your UserDevice.user_id is a numeric FK to users.id, change this to $user->id
                'user_id'   => $user->userid,
            ],
            [
                'platform'     => $request->platform,
                'device_model' => $request->device_model,
            ]
        );

        // Generate Sanctum token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message'    => 'User authenticated successfully.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $user, // includes referral_code
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

    public function sendOtpIos(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        // Create user if not exists (simple bootstrap)
        $user = User::firstOrCreate(
            ['mobile_number' => $phone],
            [
                'userid'        => $this->generateUniqueUserId(),
                'referral_code' => $this->generateReferralCodeIos(),
            ]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'OTP generated (test mode). Use 000000 to verify.',
            'user_status'  => $user->wasRecentlyCreated ? 'new' : 'existing',
            // You can expose it since this is a fixed test OTP
            'otp'          => '000000',
        ], 200);
    }

    public function verifyOtpIos(Request $request)
    {
        $validated = $request->validate([
            'phoneNumber'        => 'required|string',
            'otp'          => 'required|string|size:6',
            'device_id'    => 'nullable|string',
            'platform'     => 'nullable|string',
            'device_model' => 'nullable|string',
        ]);

        $phone = $this->normalizePhone($validated['phoneNumber']);
        $user  = User::where('mobile_number', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Mobile number not found. Please request OTP first.'
            ], 404);
        }

        // Fixed 6-digit OTP (test mode)
        if ($validated['otp'] !== '000000') {
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }

        // Optional: store device info
        if (!empty($validated['device_id'])) {
            UserDevice::updateOrCreate(
                ['device_id' => $validated['device_id'], 'user_id' => $user->userid],
                ['platform' => $validated['platform'] ?? null, 'device_model' => $validated['device_model'] ?? null]
            );
        }

        // Optional: issue Sanctum token (remove if you don't use Sanctum)
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message'    => 'User authenticated successfully.',
            'token'      => $token,         // remove if not using Sanctum
            'token_type' => 'Bearer',       // remove if not using Sanctum
            'user'       => $user,
        ], 200);
    }

    private function normalizePhone(string $raw): string
    {
        // Minimal normalization. Adjust to your market.
        $digits = preg_replace('/\D+/', '', $raw);
        // Example: assume India if 10 digits; prefix +91
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }
        return '+' . $digits;
    }

    private function generateUniqueUserId(): string
    {
        do {
            $candidate = 'USER' . random_int(10000, 99999);
        } while (User::where('userid', $candidate)->exists());
        return $candidate;
    }

    private function generateReferralCodeIos(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }

    public function updateDevice(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'version' => 'required|string',
                'last_login_at' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 500);
            }

            $user = Auth::guard('sanctum')->user();

            // Find User Device
            $device = UserDevice::where('user_id', $user->user_id)->first();

            if (!$device) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User device not found'
                ], 500);
            }

            // Update fields
            $device->version = $request->version;
            $device->device_model = $request->model;
            $device->platform = $request->os_name;
            $device->last_login_time = $request->last_login_at;
            $device->save();

            return response()->json([
                'status'  => true,
                'message' => 'Device updated successfully',
                'data'    => $device
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

}