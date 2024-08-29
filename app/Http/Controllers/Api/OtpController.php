<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDevice;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;


class OtpController extends Controller
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

            Log::info("Response Body: " . print_r($body, true));

            if (isset($body['orderId'])) {
                $orderId = $body['orderId'];

                session(['otp_order_id' => $orderId]);
                session(['otp_phone' => $phoneNumber]);

                return response()->json(['message' => 'OTP sent successfully', 'order_id' => $orderId, 'phone' => $phoneNumber], 200);
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
        // Validate the required fields
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6', // Ensure OTP is exactly 6 digits
            'device_id' => 'required|string', // Validate device_id
            'platform' => 'required|string', // Validate platform
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $orderId = $request->input('orderId');
        $otp = $request->input('otp');
        $phoneNumber = $request->input('phoneNumber');
        $deviceId = $request->input('device_id');
        $platform = $request->input('platform');
        $device_model = $request->input('device_model');

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
                $user = User::where('mobile_number', $phoneNumber)->first();

                if (!$user) {
                    $user = User::create([
                        'userid' => 'USER' . rand(10000, 99999),
                        'mobile_number' => $phoneNumber,
                        'order_id' => $orderId,
                    ]);
                }

                // Update or create a device record for the user
                // $user->devices()->updateOrCreate(
                //     [
                //         'device_id' => $deviceId,
                //         'platform' => $platform
                //     ], // Condition to find the existing record
                //     ['user_id' => $user->userid] // Data to update or create
                // );

                $user->devices()->create([
                    'user_id' => $user->userid,
                    'device_id' => $deviceId,
                    'platform' => $platform,
                    'device_model' => $device_model
                ]);
    

                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'message' => 'User authenticated successfully.',
                    'user' => $user,
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

    public function userLogout(Request $request)
    {
        $user = Auth::guard('sanctum')->user(); // Get the authenticated user
        $deviceId = $request->input('device_id'); // Received from the client

        try {
            // Manually find the device entry by user_id and device_id without using the relationship
            $device = UserDevice::where('user_id', $user->userid)
                ->where('device_id', $deviceId)
                ->first();

            if ($device) {
                // Delete the device entry
                $device->delete();

                // Revoke all tokens for the user (logout from all devices)
                $user->tokens()->delete();

                Log::info('User logged out successfully and device removed.', ['user_id' => $user->id, 'device_id' => $deviceId]);

                return response()->json([
                    'status' => 200,
                    'message' => 'User logged out successfully and device removed.'
                ], 200);
            } else {
                Log::warning('Device not found for user.', ['user_id' => $user->id, 'device_id' => $deviceId]);

                return response()->json([
                    'status' => 404,
                    'message' => 'Device not found.'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while logging out the user.', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while logging out.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    
}

