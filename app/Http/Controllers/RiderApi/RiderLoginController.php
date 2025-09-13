<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\RiderDetails;
use App\Models\DeliveryHistory;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class RiderLoginController extends Controller
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
    //         // 'orderId' => 'required',
    //         // 'phoneNumber' => 'required|digits:10', // Ensure phone number is valid
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }
    
    //     $orderId = $request->input('orderId');
    //     $otp = $request->input('otp');
    //     $phoneNumber = $request->input('phoneNumber');
    
    //     $client = new Client();
    //     $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/verify';
    
    //     try {
    //         // Send OTP verification request to external API
    //         $response = $client->post($url, [
    //             'headers' => [
    //                 'Content-Type' => 'application/json',
    //                 'clientId' => $this->clientId,
    //                 'clientSecret' => $this->clientSecret,
    //             ],
    //             'json' => [
    //                 'orderId' => $orderId,
    //                 'otp' => $otp,
    //                 'phoneNumber' => $phoneNumber,
    //             ],
    //         ]);
    
    //         $body = json_decode($response->getBody(), true);
    //         Log::info("Response Body: " . print_r($body, true));
    
    //         // Check if OTP is verified successfully
    //         if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
    //             // Check if rider exists
    //             $rider = RiderDetails::where('phone_number', $phoneNumber)->first();
    
    //             if (!$rider) {
    //                 // Rider not found, return message without creating a new rider
    //                 return response()->json([
    //                     'status' => 404,
    //                     'message' => 'You are not registered, contact admin.',
    //                 ], 404);
    //             }
    
    //             // Generate a token
    //             $token = $rider->createToken('API Token')->plainTextToken;
    
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Rider authenticated successfully.',
    //                 'rider' => $rider,
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

    public function riderSendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits_between:10,15',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $phone = $request->input('phone');

        // Store phone in session (optional, if you need it later)
        session(['otp_phone' => $phone]);

        // Static OTP (always 000000)
        $otp = '000000';

        Log::info("OTP sent to {$phone}: {$otp}");

        return response()->json([
            'status'  => 200,
            'message' => 'OTP sent successfully (use 000000)',
            'phone'   => $phone,
        ], 200);
    }

    /**
     * Verify OTP (static check)
     */
    public function riderVerifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits_between:10,15',
            'otp'   => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $phone = $request->input('phone');
        $otp   = $request->input('otp');

        // ✅ Check static OTP
        if ($otp !== '000000') {
            return response()->json(['status' => 400, 'message' => 'Invalid OTP'], 400);
        }

        // ✅ Check rider in DB
        $rider = RiderDetails::where('phone_number', $phone)->first();

        if (!$rider) {
            return response()->json([
                'status'  => 404,
                'message' => 'You are not registered, contact admin.',
            ], 404);
        }

        // ✅ Generate token
        $token = $rider->createToken('API Token')->plainTextToken;

        return response()->json([
            'status'     => 200,
            'message'    => 'Rider authenticated successfully.',
            'rider'      => $rider,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
    
    // public function getRiderDetails()
    // {
    //     try {
    //         // Get the authenticated rider
    //         $rider = Auth::guard('rider-api')->user();

    //         if (!$rider) {
    //             return response()->json([
    //                 'status' => 401,
    //                 'message' => 'Unauthorized',
    //             ], 401);
    //         }

    //         // Retrieve rider details
    //         $riderDetails = RiderDetails::where('rider_id', $rider->rider_id)->first();

    //         if (!$riderDetails) {
    //             return response()->json([
    //                 'status' => 404,
    //                 'message' => 'Rider details not found',
    //             ], 404);
    //         }

    //         // Generate the full image URL
    //         $riderDetails->rider_img = $riderDetails->rider_img 
    //             ? url('storage/' . $riderDetails->rider_img) 
    //             : null;

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Rider details fetched successfully',
    //             'data' => $riderDetails,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function getRiderDetails()
    {
        try {
            // Get the authenticated rider
            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Retrieve rider details
            $riderDetails = RiderDetails::where('rider_id', $rider->rider_id)->first();

            if (!$riderDetails) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Rider details not found',
                ], 404);
            }

            // Generate the full image URL
            $riderDetails->rider_img = $riderDetails->rider_img 
                ? url('storage/' . $riderDetails->rider_img) 
                : null;

            // Get total deliveries done by the rider
            $totalDeliveries = DeliveryHistory::where('rider_id', $rider->rider_id)
                ->count();

            // Get deliveries done by the rider in the current month
            $currentMonthDeliveries = DeliveryHistory::where('rider_id', $rider->rider_id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'status' => 200,
                'message' => 'Rider details fetched successfully',
                'data' => [
                    'riderDetails' => $riderDetails,
                    'totalDeliveries' => $totalDeliveries,
                    'currentMonthDeliveries' => $currentMonthDeliveries,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
