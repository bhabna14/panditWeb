<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\PanditLogin;
use App\Models\PanditDevice;
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
        $fullPhoneNumber = $request->input('phone');
        // $countryCode = '+91'; // Assuming the country code is +91
        // $fullPhoneNumber = $countryCode . $phoneNumber;
    
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
    
    
    // public function verifyOtp(Request $request)
    // {
    //     $orderId = $request->input('orderId');
    //     $otp = $request->input('otp');
    //     $phoneNumber = $request->input('phoneNumber');
    
    //     // Log the inputs for debugging
    //     Log::info("Verifying OTP for Order ID: " . $orderId . ", Phone Number: " . $phoneNumber . ", OTP: " . $otp);
    
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
    //             $pandit = PanditLogin::where('mobile_no', $phoneNumber)->first();
    
    //             if (!$pandit) {
    //                 $pandit = PanditLogin::create([
    //                     'pandit_id' => 'PANDIT' . rand(10000, 99999),
    //                     'mobile_no' => $phoneNumber,
    //                     'order_id' => $orderId,
    //                 ]);
    //             }
    
    //             $token = $pandit->createToken('API Token')->plainTextToken;
    
    //             return response()->json([
    //                 'message' => 'Pandit authenticated successfully.', 
    //                 'user' => $pandit,
    //                 'token' => $token, 
    //                 'token_type' => 'Bearer'], 200);
    //         } else {
    //             $message = $body['message'] ?? 'Invalid OTP';
    //             return response()->json(['message' => $message], 400);
    //         }
    //     } catch (RequestException $e) {
    //         Log::error("Request Exception: " . $e->getMessage());
    //         return response()->json(['message' => 'Failed to verify OTP due to an error.'], 500);
    //     }
    // }
    
    public function verifyOtp(Request $request)
    {
        $orderId = $request->input('orderId');
        $otp = $request->input('otp');
        $phoneNumber = $request->input('phoneNumber');
        $deviceId = $request->input('device_id'); // Received from the client
        $platform = $request->input('platform'); // 'web', 'android', or 'ios'
        $device_model = $request->input('device_model');
    
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
                    // Create a new PanditLogin record if it doesn't exist
                    $pandit = PanditLogin::create([
                        'pandit_id' => 'PANDIT' . rand(10000, 99999),
                        'mobile_no' => $phoneNumber,
                        'order_id' => $orderId,
                        'status' => 'active', // Set status to active for new record
                    ]);
                } else {
                    // If Pandit already exists, update the status to active
                    $pandit->status = 'active';
                    $pandit->save();
                }
    
                // Update or insert device info
                // $pandit->devices()->updateOrCreate(
                //     ['pandit_id' => $pandit->pandit_id],
                //     ['device_id' => $deviceId],
                //     ['platform' => $platform]
                // );

                $pandit->devices()->create([
                    'pandit_id' => $pandit->pandit_id,
                    'device_id' => $deviceId,
                    'platform' => $platform,
                    'device_model' => $device_model
                ]);
    
    
                // Generate token
                $token = $pandit->createToken('API Token')->plainTextToken;
    
                return response()->json([
                    'message' => 'Pandit authenticated successfully.',
                    'user' => $pandit,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ], 200);
            } else {
                $message = $body['message'] ?? 'Invalid OTP';
                return response()->json(['message' => $message], 400);
            }
        } catch (RequestException $e) {
            Log::error("Request Exception: " . $e->getMessage());
            return response()->json(['message' => 'Failed to verify OTP due to an error.'], 500);
        }
    }
    
    
    // public function panditLogout()
    // {
    //     // Retrieve the pandit ID using the 'pandits' auth guard.
    //     $pandit_id = Auth::guard('sanctum')->user()->pandit_id;
    
    //     if (!$pandit_id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pandit ID not found.',
    //         ], 404);
    //     }
    
    //     // Retrieve the PanditLogin record for the logged-in Pandit.
    //     $panditLogin = PanditLogin::where('pandit_id', $pandit_id)->first();
    
    //     if (!$panditLogin) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pandit login record not found.',
    //         ], 404);
    //     }
    
    //     $panditLogin->status = 'inactive';
    
    //     if ($panditLogin->save()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Pandit logged out successfully.',
    //             'status' => $panditLogin->status, 
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to logout Pandit.',
    //         ], 500);
    //     }
    // }

    public function panditLogout(Request $request)
    {
        // Get the authenticated pandit
        $pandit = Auth::guard('sanctum')->user(); 
        $deviceId = $request->input('device_id'); // Received from the client
    
        // Log the pandit ID and device ID for debugging
        Log::info("Pandit Logout Attempt:", [
            'pandit_id' => $pandit->pandit_id, 
            'device_id' => $deviceId
        ]);
    
        try {
            // Manually find the device entry by pandit_id and device_id without using the relationship
            $device = PanditDevice::where('pandit_id', $pandit->pandit_id)
                ->where('device_id', $deviceId)
                ->first();
    
            if ($device) {
                // Log that the device was found
                Log::info("Device found. Proceeding with logout.", [
                    'device_id' => $deviceId
                ]);
    
                // Delete the device entry
                $device->delete();
    
                // Revoke all tokens for the pandit (logout from all devices)
                $pandit->tokens()->delete();
    
                // Log successful logout
                Log::info("Pandit logged out successfully and device removed.", [
                    'pandit_id' => $pandit->pandit_id, 
                    'device_id' => $deviceId
                ]);
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Pandit logged out successfully and device removed.'
                ], 200);
            } else {
                // Log that the device was not found
                Log::warning("Device not found for logout.", [
                    'pandit_id' => $pandit->pandit_id, 
                    'device_id' => $deviceId
                ]);
    
                return response()->json([
                    'status' => 404,
                    'message' => 'Device not found.'
                ], 404);
            }
        } catch (\Exception $e) {
            // Log the error details
            Log::error('An error occurred while logging out the pandit.', [
                'pandit_id' => $pandit->pandit_id, 
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
    
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while logging out.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    
}
