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
        // Validate the phone number
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10', // Assuming phone numbers should be 10 digits
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $phoneNumber = $request->input('phone');
        $countryCode = $request->input('country_code');
        $fullPhoneNumber = $countryCode . $phoneNumber;
    
        $client = new Client();
    
        $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/send';
    
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'clientId'      => $this->clientId,
                    'clientSecret'  => $this->clientSecret,
                ],
                'json' => [
                    'phoneNumber' => $fullPhoneNumber,
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
    
            if (isset($body['orderId'])) {
                $orderId = $body['orderId'];
    
                // Store $orderId in session or pass it along to OTP verification form
                session(['otp_order_id' => $orderId]);
                session(['otp_phone' => $fullPhoneNumber]);
    
                return redirect()->back()->with('otp_sent', true)->with('message', 'OTP sent successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to send OTP. Please try again.');
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $errorMessage = $responseBody['message'] ?? 'Failed to send OTP due to an error.';
    
            return redirect()->back()->with('error', $errorMessage);
        }
    }
    
   

public function verifyOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'otp' => 'required|digits:6',
        'device_id' => 'required|string',
        'platform' => 'required|string',
        'device_model' => 'required|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $phoneNumber = session('otp_phone');
    $otp = $request->input('otp');

    if (!$phoneNumber) {
        return redirect()->back()->with('error', 'Session expired. Please request a new OTP.');
    }

    $user = User::where('mobile_number', $phoneNumber)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Mobile number not found. Please request OTP first.');
    }

    if ($user->otp !== $otp) {
        return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
    }

    // OTP matched — clear it
    $user->otp = null;
    $user->save();

    // Save device info
    UserDevice::updateOrCreate(
        ['device_id' => $request->device_id, 'user_id' => $user->userid],
        [
            'platform' => $request->platform,
            'device_model' => $request->device_model,
        ]
    );

    // Authenticate the user using a custom guard or default
    Auth::guard('users')->login($user);

    $referer = session()->pull('login_referer', route('userindex'));

    return redirect(urldecode($referer))->with('success', 'User authenticated successfully.');
}

}


