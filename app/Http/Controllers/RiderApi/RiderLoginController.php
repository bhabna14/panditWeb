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

    public function riderSendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $phone = $request->input('phone');

        // 🔹 Normalize phone → remove +91 / 91 prefix
        $phone = preg_replace('/^\+?91/', '', $phone);

        // 🔹 Ensure only digits, trim spaces
        $phone = preg_replace('/\D/', '', $phone);

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

    public function riderVerifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required|string|digits_between:10,15',
            'otp'   => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $phone = $request->input('phoneNumber');

        // 🔹 Normalize phone → remove +91 / 91 prefix
        $phone = preg_replace('/^\+?91/', '', $phone);

        // 🔹 Ensure only digits
        $phone = preg_replace('/\D/', '', $phone);

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
