<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\RequestException;


class OtplessLoginController extends Controller
{
    //
    public function otplogin(){
        return view('otp-login');
    }
    private $apiUrl = 'https://auth.otpless.app';
    private $clientId = 'Q9Z0F0NXFT3KG3IHUMA4U4LADMILH1CB';
    private $clientSecret = '5rjidx7nav2mkrz9jo7f56bmj8zuc1r2';

    // public function sendOtp(Request $request)
    // {
    //     $phone = $request->input('phone');
    //     $client = new Client();
        
    //     $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/send';

    //     // Debugging: Print the URL
    //     logger("Sending OTP to URL: $url");

    //     $response = $client->post($url, [
    //         'headers' => [
    //             'Content-Type'  => 'application/json',
    //             'clientId'      => $this->clientId,
    //             'clientSecret'  => $this->clientSecret,
    //         ],
    //         'json' => [
    //             'phoneNumber' => $phone,
    //         ],
    //     ]);

    //     $body = json_decode($response->getBody(), true);

    //     // if ($body['success']) {
    //         return redirect()->back()->with('otp_sent', true)->with('phone', $phone);
    //     // } else {
    //     //     return redirect()->back()->with('message', 'Failed to send OTP');
    //     // }
    // }

  

    // public function verifyOtp(Request $request)
    // {
    //     $orderId = $request->input('order_id');
    //     $otp = $request->input('otp');
    //     $phoneNumber = $request->input('phone');
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
    
    //         // Debugging: Print the response body
    //         // dd("Response Body: " . print_r($body, true));
    //         // dd
    
    //         if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
    //             // Check if user already exists
    //             $user = User::where('mobile_number', $phoneNumber)->first();
    //             // $userid = ;
    //             if (!$user) {
    //                 // User does not exist, create a new user
    //                 $user = User::create([
    //                     'userid' => 'USER' . rand(10000, 99999),
    //                     'mobile_number' => $phoneNumber,
    //                     'order_id' => $orderId,
    //                 ]);
    //             }
    
    //             // Optionally, log the user in or redirect to another page
    //             // Example: Auth::login($user);
    
    //             return redirect()->route('userindex')->with('success', 'OTP verified successfully.');
    //         } else {
    //             $message = $body['message'] ?? 'Invalid OTP';
    //             return redirect()->back()->with('message', $message);
    //         }
    //     } catch (RequestException $e) {
    //         // Debugging: Print the error message
    //         logger("Request Exception: " . $e->getMessage());
    //         return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
    //     }
    // }

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
    
    
    // public function verifyOtp(Request $request)
    // {
    //     // Validate the OTP length
    //     $validator = Validator::make($request->all(), [
    //         'otp' => 'required|digits:6', // Ensure OTP is exactly 6 digits
    //     ]);
    
    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }
    
    //     $orderId = $request->session()->get('otp_order_id');
    //     $otp = $request->input('otp');
    //     $phoneNumber = $request->session()->get('otp_phone');
    
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
    
    //         // Debugging: Print the response body
    //         logger("Response Body: " . print_r($body, true));
    
    //         if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
    //             // Check if user already exists
    //             $user = User::where('mobile_number', $phoneNumber)->first();
    
    //             if (!$user) {
    //                 // User does not exist, create a new user
    //                 $user = User::create([
    //                     'userid' => 'USER' . rand(10000, 99999),
    //                     'mobile_number' => $phoneNumber,
    //                     'order_id' => $orderId,
    //                 ]);
    //             }
    
    //             // Log the user in using the custom guard
    //             Auth::guard('users')->login($user);
    
    //             // Redirect to the intended page or home page
    //             return redirect()->route('userindex')->with('success', 'User authenticated successfully.');
    //         } else {
    //             return redirect()->back()->with('message', 'Wrong OTP. Please try again.');
    //         }
    //     } catch (RequestException $e) {
    //         // Debugging: Print the error message
    //         logger("Request Exception: " . $e->getMessage());
    //         return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
    //     }
    // }
    
    public function verifyOtp(Request $request)
    {
        // Validate the OTP length
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6', // Ensure OTP is exactly 6 digits
            // 'device_id' => 'required|string', // Validate device_id
            // 'platform' => 'required|string', // Validate platform
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $orderId = $request->session()->get('otp_order_id');
        $otp = $request->input('otp');
        $phoneNumber = $request->session()->get('otp_phone');
        $deviceId = $request->input('device_id');
        $platform = $request->input('platform');
    
        $client = new Client();
    
        $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/verify';
    
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'clientId'      => $this->clientId,
                    'clientSecret'  => $this->clientSecret,
                ],
                'json' => [
                    'orderId' => $orderId,
                    'otp' => $otp,
                    'phoneNumber' => $phoneNumber,
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
    
            if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
                // Check if user already exists
                $user = User::where('mobile_number', $phoneNumber)->first();
    
                if (!$user) {
                    // User does not exist, create a new user
                    $user = User::create([
                        'userid' => 'USER' . rand(10000, 99999),
                        'mobile_number' => $phoneNumber,
                        'order_id' => $orderId,
                    ]);
                }
    
                // Save device_id and platform to user_devices table
                $user->devices()->updateOrCreate(
                    [
                        'device_id' => $deviceId,
                        'platform' => $platform
                    ], // Condition to find the existing record
                    ['user_id' => $user->userid] // Data to update or create
                );
    
                // Log the user in using the custom guard
                Auth::guard('users')->login($user);
    
                // Get the referer URL from the session
                $referer = session()->get('login_referer', route('userindex'));
    
                // Decode the URL once
                $referer = urldecode($referer);
    
                // Clear the referer from the session after using it
                $request->session()->forget('login_referer');
    
                // Redirect to the referer URL or home page if not available
                return redirect($referer)->with('success', 'User authenticated successfully.');
            } else {
                return redirect()->back()->with('message', 'Wrong OTP. Please try again.');
            }
        } catch (RequestException $e) {
            return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
        }
    }
    
    
    

    
}


