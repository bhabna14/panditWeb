<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\PanditLogin;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanditLoginController extends Controller
{
    private $apiUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->apiUrl = 'https://auth.otpless.app';
        $this->clientId = 'Q9Z0F0NXFT3KG3IHUMA4U4LADMILH1CB';
        $this->clientSecret = '5rjidx7nav2mkrz9jo7f56bmj8zuc1r2';
    }

    public function sendOtp(Request $request)
    {
        $phoneNumber = $request->input('phone');
        $fullPhoneNumber =  $phoneNumber; // Assuming the country code is +91
    
        // Log the full phone number for debugging
        Log::info("Sending OTP to: " . $fullPhoneNumber);
    
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
    
            Log::info("Response Body: " . print_r($body, true));
    
            if (isset($body['orderId'])) {
                $orderId = $body['orderId'];
    
                session(['otp_order_id' => $orderId]);
                session(['otp_phone' => $fullPhoneNumber]);
    
                return response()->json(['message' => 'OTP sent successfully', 'order_id' => $orderId, 'phone' => $fullPhoneNumber], 200);
            } else {
                return response()->json(['message' => 'Failed to send OTP. Please try again.'], 400);
            }
        } catch (RequestException $e) {
            Log::error("Request Exception: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP due to an error.'], 500);
        }
    }
    
    
    public function verifyOtp(Request $request)
    {
        $orderId = $request->input('orderId');
        $otp = $request->input('otp');
        $phoneNumber = $request->input('phoneNumber');
    
        // Log the inputs for debugging
        Log::info("Verifying OTP for Order ID: " . $orderId . ", Phone Number: " . $phoneNumber . ", OTP: " . $otp);
    
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
    
            Log::info("Response Body: " . print_r($body, true));
    
            if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
                $pandit = PanditLogin::where('mobile_no', $phoneNumber)->first();
    
                if (!$pandit) {
                    $pandit = PanditLogin::create([
                        'pandit_id' => 'PANDIT' . rand(10000, 99999),
                        'mobile_no' => $phoneNumber,
                        'order_id' => $orderId,
                    ]);
                }
    
                $token = $pandit->createToken('API Token')->plainTextToken;
    
                return response()->json([
                    'message' => 'Pandit authenticated successfully.', 
                    'user' => $pandit,
                    'token' => $token, 
                    'token_type' => 'Bearer'], 200);
            } else {
                $message = $body['message'] ?? 'Invalid OTP';
                return response()->json(['message' => $message], 400);
            }
        } catch (RequestException $e) {
            Log::error("Request Exception: " . $e->getMessage());
            return response()->json(['message' => 'Failed to verify OTP due to an error.'], 500);
        }
    }
    
    
}
