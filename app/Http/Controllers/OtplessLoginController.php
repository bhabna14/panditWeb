<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;

class OtplessLoginController extends Controller
{
    //
    public function otplogin(){
        return view('otp-login');
    }

 public function sendOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'phone' => 'required|digits:10', // Assuming 10-digit phone number
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $phone = $request->input('phone');
    $otp = rand(100000, 999999);
    $shortToken = Str::random(6); // Used in button

    // Step 2: Find or create user
    $user = User::where('mobile_number', $phone)->first();
    if ($user) {
        $user->otp = $otp;
        $user->save();
        $status = 'existing';
    } else {
        $user = User::create([
            'mobile_number' => $phone,
            'otp' => $otp,
            'userid' => 'USER' . rand(10000, 99999),
        ]);
        $status = 'new';
    }

    // Step 3: Prepare MSG91 WhatsApp payload
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

    // Step 4: Send request to MSG91
    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'authkey' => env('MSG91_AUTHKEY'),
        ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/', $payload);

        $result = $response->json();

        if ($response->status() === 401 || ($result['status'] ?? '') === 'fail') {
            return redirect()->back()->with('error', 'Unauthorized: Check MSG91 credentials or template settings.');
        }

        // Step 5: Store OTP session data (if needed for verify step)
        session([
            'otp_phone' => $phone,
        ]);

        return redirect()->back()->with('otp_sent', true)->with('message', 'OTP sent successfully via WhatsApp.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to send OTP: ' . $e->getMessage());
    }
}
public function verifyOtp(Request $request)
{
    // Step 1: Validate input
    $validator = Validator::make($request->all(), [
        'otp' => 'required|digits:6',
        'device_id' => 'required|string',
        'platform' => 'required|string',
        'device_model' => 'required|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Step 2: Get phone from session
    $phoneNumber = session('otp_phone');
    $otp = $request->input('otp');

    if (!$phoneNumber) {
        return redirect()->back()->with('error', 'Session expired. Please request a new OTP.');
    }

    // Step 3: Fetch user by mobile number
    $user = User::where('mobile_number', $phoneNumber)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Mobile number not found. Please request OTP again.');
    }

    // Step 4: Check OTP
    if ($user->otp !== $otp) {
        return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
    }

    // Step 5: OTP matched — clear OTP
    $user->otp = null;
    $user->save();

    // Step 6: Save device info
    UserDevice::updateOrCreate(
        ['device_id' => $request->device_id, 'user_id' => $user->userid],
        [
            'platform' => $request->platform,
            'device_model' => $request->device_model,
        ]
    );

    // Step 7: Login the user using custom guard
    Auth::guard('users')->login($user);

    // Optional: Create Sanctum token if you need it later (e.g., for frontend JavaScript usage)
    // $token = $user->createToken('Web Token')->plainTextToken;
    // session(['auth_token' => $token]);

    // Step 8: Redirect to referer or default
    $referer = session()->pull('login_referer', route('userindex'));
    return redirect(urldecode($referer))->with('success', 'User authenticated successfully.');
}
}


