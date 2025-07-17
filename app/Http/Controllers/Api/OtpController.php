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
    // private $apiUrl;
    // private $clientId;
    // private $clientSecret;

    // public function __construct()
    // {
    //     $this->apiUrl = 'https://auth.otpless.app';
    //     $this->clientId = 'Q9Z0F0NXFT3KG3IHUMA4U4LADMILH1CB';
    //     $this->clientSecret = '5rjidx7nav2mkrz9jo7f56bmj8zuc1r2';
    // }

    // public function sendOtp(Request $request)
    // {
    //     $phoneNumber = $request->input('phone');
    //     $client = new Client();

    //     $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/send';

    //     try {
    //         $response = $client->post($url, [
    //             'headers' => [
    //                 'Content-Type'  => 'application/json',
    //                 'clientId'      => $this->clientId,
    //                 'clientSecret'  => $this->clientSecret,
    //             ],
    //             'json' => [
    //                 'phoneNumber' => $phoneNumber,
    //             ],
    //         ]);

    //         $body = json_decode($response->getBody(), true);

    //         Log::info("Response Body: " . print_r($body, true));

    //         if (isset($body['orderId'])) {
    //             $orderId = $body['orderId'];

    //             session(['otp_order_id' => $orderId]);
    //             session(['otp_phone' => $phoneNumber]);

    //             return response()->json(['message' => 'OTP sent successfully', 'order_id' => $orderId, 'phone' => $phoneNumber], 200);
    //         } else {
    //             return response()->json(['message' => 'Failed to send OTP. Please try again.'], 400);
    //         }
    //     } catch (RequestException $e) {
    //         Log::error("Request Exception: " . $e->getMessage());
    //         return response()->json(['message' => 'Failed to send OTP due to an error.'], 500);
    //     }
    // }

    // public function verifyOtp(Request $request)
    // {
    //     // Validate the required fields
    //     $validator = Validator::make($request->all(), [
    //         'otp' => 'required|digits:6', // Ensure OTP is exactly 6 digits
    //         'device_id' => 'required|string', // Validate device_id
    //         'platform' => 'required|string', // Validate platform
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }

    //     $orderId = $request->input('orderId');
    //     $otp = $request->input('otp');
    //     $phoneNumber = $request->input('phoneNumber');
    //     $deviceId = $request->input('device_id');
    //     $platform = $request->input('platform');
    //     $device_model = $request->input('device_model');

    //     $client = new Client();
    //     $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/verify';

    //     try {
    //         $response = $client->post($url, [
    //             'headers' => [
    //                 'Content-Type'  => 'application/json',
    //                 'clientId'      => $this->clientId,
    //                 'clientSecret'  => $this->clientSecret,
    //             ],
    //             'json' => [
    //                 'orderId' => $orderId,
    //                 'otp' => $otp,
    //                 'phoneNumber' => $phoneNumber,
    //             ],
    //         ]);

    //         $body = json_decode($response->getBody(), true);
    //         Log::info("Response Body: " . print_r($body, true));

    //         if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
    //             $user = User::where('mobile_number', $phoneNumber)->first();

    //             if (!$user) {
    //                 $user = User::create([
    //                     'userid' => 'USER' . rand(10000, 99999),
    //                     'mobile_number' => $phoneNumber,
    //                     'order_id' => $orderId,
    //                 ]);
    //             }

    //             $user->devices()->create([
    //                 'user_id' => $user->userid,
    //                 'device_id' => $deviceId,
    //                 'platform' => $platform,
    //                 'device_model' => $device_model
    //             ]);
    //             $token = $user->createToken('API Token')->plainTextToken;

    //             return response()->json([
    //                 'message' => 'User authenticated successfully.',
    //                 'user' => $user,
    //                 'token' => $token,
    //                 'token_type' => 'Bearer'
    //             ], 200);
    //         } else {
    //             $message = $body['message'] ?? 'Invalid OTP';
    //             return response()->json(['message' => $message], 400);
    //         }
    //     } catch (RequestException $e) {
    //         Log::error("Request Exception: " . $e->getMessage());
    //         return response()->json(['message' => 'Failed to verify OTP due to an error.'], 500);
    //     }
    // }

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

    //  public function sendOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|string',
    //     ]);

    //     $otp = rand(100000, 999999);
    //     $phone = 'whatsapp:' . $request->phone;

    //     // Store OTP in cache for 5 minutes
    //     Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(5));

    //     $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

    //     $twilio->messages->create(
    //         $phone,
    //         [
    //             'from' => env('TWILIO_WHATSAPP_FROM'),
    //             'body' => "Your WhatsApp OTP is: $otp"
    //         ]
    //     );

    //     return response()->json(['message' => 'OTP sent successfully.']);
    // }

    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|string',
    //         'otp' => 'required|string',
    //     ]);

    //     $cachedOtp = Cache::get('otp_' . $request->phone);

    //     if ($cachedOtp && $cachedOtp == $request->otp) {
    //         Cache::forget('otp_' . $request->phone);
    //         return response()->json(['message' => 'OTP verified successfully.']);
    //     }

    //     return response()->json(['message' => 'Invalid or expired OTP.'], 401);
    // }

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

        // Try to find existing user
        $user = User::where('mobile_number', $request->mobile_number)->first();

        // If user does not exist, create a new one with a generated userid
        if (!$user) {
            $user = User::create([
                'mobile_number' => $request->phoneNumber,
                'otp' => $request->otp,
                'pratihari_id' => 'USER' . rand(10000, 99999),
            ]);
        }

        // Now check if the OTP is correct
        if ($user->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP or mobile number.'
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
            'token_type' => 'Bearer'
        ], 200);
    }

}