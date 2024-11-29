<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\RiderDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class RiderLoginController extends Controller
{
    //
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
        // dd("hi");
        // Validate the required fields
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6', // Ensure OTP is exactly 6 digits
            // 'orderId' => 'required',
            // 'phoneNumber' => 'required|digits:10', // Ensure phone number is valid
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
    
        $orderId = $request->input('orderId');
        $otp = $request->input('otp');
        $phoneNumber = $request->input('phoneNumber');
    
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
                // Check if rider exists
                $rider = RiderDetails::where('phone_number', $phoneNumber)->first();
    
                if (!$rider) {
                    // Create a new rider if not found
                    $rider = RiderDetails::create([
                        'rider_id' => 'RIDER' . rand(10000, 99999),
                        'phone_number' => $phoneNumber,
                        'rider_name' => null, // Placeholder, as it's required by your schema
                        'rider_img' => null, // Default value
                        'description' => null, // Default value
                    ]);
                }
    
                // Generate a token
                // $token = $rider->createToken('API Token')->plainTextToken;
                $token = $rider->createToken('API Token')->plainTextToken;
    
                return response()->json([
                    'message' => 'Rider authenticated successfully.',
                    'rider' => $rider,
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
    
    public function getRiderDetails()
    {
        try {
            // Get the authenticated rider
            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Retrieve rider details
            $riderDetails = RiderDetails::where('rider_id', $rider->rider_id)->first();

            if (!$riderDetails) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rider details not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Rider details fetched successfully',
                'data' => $riderDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
