<?php

namespace App\Http\Controllers\pandit;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\PanditLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;

class PanditOtpController extends Controller
{
    private $apiUrl = 'https://auth.otpless.app';
    private $clientId = 'Q9Z0F0NXFT3KG3IHUMA4U4LADMILH1CB';
    private $clientSecret = '5rjidx7nav2mkrz9jo7f56bmj8zuc1r2';

    public function sendOtp(Request $request)
    {
        $phoneNumber = $request->input('phone');
        $countryCode = '+91'; // Assuming the country code is +91 as in your Blade template
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
                session(['otp_order_id' => $orderId, 'otp_phone' => $fullPhoneNumber]);
                return redirect()->back()->with('otp_sent', true)->with('message', 'OTP sent successfully');
            } else {
                return redirect()->back()->with('message', 'Failed to send OTP. Please try again.');
            }
        } catch (RequestException $e) {
            return redirect()->back()->with('message', 'Failed to send OTP due to an error.');
        }
    }

    // public function verifyOtp(Request $request)
    // {
    //     $orderId = session('otp_order_id');
    //     $otp = $request->input('otp');
    //     $phoneNumber = session('otp_phone');
    
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
    
    //         if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
    //             $pandit = PanditLogin::where('mobile_no', $phoneNumber)->first();
    
    //             if ($pandit) {
    //                 // Pandit already exists, log in and redirect to dashboard
    //                 Auth::guard('pandits')->login($pandit);
    //                 return redirect()->route('pandit.dashboard')->with('success', 'User authenticated successfully.');
    //             } else {
    //                 // Pandit does not exist, create a new user
    //                 $pandit = PanditLogin::create([
    //                     'pandit_id' => 'PANDIT' . rand(10000, 99999),
    //                     'mobile_no' => $phoneNumber,
    //                     'order_id' => $orderId,
    //                 ]);
    
    //                 // Log the new pandit in and redirect to profile page
    //                 Auth::guard('pandits')->login($pandit);
    //                 return redirect()->route('pandit.profile')->with('success', 'User authenticated successfully.');
    //             }
    //         } else {
    //             $message = $body['message'] ?? 'Invalid OTP';
    //             return redirect()->back()->with('message', $message);
    //         }
    //     } catch (RequestException $e) {
    //         return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
    //     }
    // }

    public function verifyOtp(Request $request)
{
    $orderId = session('otp_order_id');
    $otp = $request->input('otp');
    $phoneNumber = session('otp_phone');
    $oneSignalPlayerId = $request->input('onesignal_player_id'); // Capture the OneSignal Player ID
    // Debugging: Check if the OneSignal Player ID is received
    // if (empty($oneSignalPlayerId)) {
    //     return redirect()->back()->with('message', 'OneSignal Player ID is missing.');
    // }
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
            $pandit = PanditLogin::where('mobile_no', $phoneNumber)->first();

            if ($pandit) {
                // Update the OneSignal Player ID if it exists
                $pandit->update(['onesignal_player_id' => $oneSignalPlayerId]);

                // Log the user in
                Auth::guard('pandits')->login($pandit);
                return redirect()->route('pandit.dashboard')->with('success', 'User authenticated successfully.');
            } else {
                // Create a new pandit with OneSignal Player ID
                $pandit = PanditLogin::create([
                    'pandit_id' => 'PANDIT' . rand(10000, 99999),
                    'mobile_no' => $phoneNumber,
                    'order_id' => $orderId,
                    'onesignal_player_id' => $oneSignalPlayerId, // Store the OneSignal Player ID
                ]);

                // Log the new pandit in
                Auth::guard('pandits')->login($pandit);
                return redirect()->route('pandit.profile')->with('success', 'User authenticated successfully.');
            }
        } else {
            $message = $body['message'] ?? 'Invalid OTP';
            return redirect()->back()->with('message', $message);
        }
    } catch (RequestException $e) {
        return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
    }
}


    public function showOtpForm()
    {
        return view('otp-login');
    }
}
