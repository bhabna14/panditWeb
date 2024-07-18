<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


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
        $phoneNumber = $request->input('phone');
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
                    'phoneNumber' => $phoneNumber,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            // Debugging: Print the response body
            logger("Response Body: " . print_r($body, true));

            if (isset($body['orderId'])) {
                $orderId = $body['orderId'];
                // $phoneNumber= $body['phoneNumber'];

                // Store $orderId in session or pass it along to OTP verification form
                // For example, store it in session
                session(['otp_order_id' => $orderId]);
                // session(['phoneNumber' => $phoneNumber]);
                session(['otp_phone' => $phoneNumber]);

                // Redirect to OTP verification form with orderId as input value
                // return redirect()->route('otp_sent')->with('message', 'OTP sent successfully');
                return redirect()->back()->with('otp_sent', true)->with('message', 'OTP sent successfully');
            } else {
                return redirect()->back()->with('message', 'Failed to send OTP. Please try again.');
            }
        } catch (RequestException $e) {
            // Debugging: Print the error message
            logger("Request Exception: " . $e->getMessage());
            return redirect()->back()->with('message', 'Failed to send OTP due to an error.');
        }
    }

    public function verifyOtp(Request $request)
    {
        $orderId = $request->input('order_id');
        $otp = $request->input('otp');
        $phoneNumber = $request->input('phone');
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
    
            // Debugging: Print the response body
            // dd("Response Body: " . print_r($body, true));
    
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
    
                // Log the user in using the custom guard
                Auth::guard('users')->login($user);
    
                // Redirect to the intended page or home page
                return redirect()->route('userindex')->with('success', 'User authenticated successfully.');
            } else {
                $message = $body['message'] ?? 'Invalid OTP';
                return redirect()->back()->with('message', $message);
            }
        } catch (RequestException $e) {
            // Debugging: Print the error message
            logger("Request Exception: " . $e->getMessage());
            return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
        }
    }
    

    
}


