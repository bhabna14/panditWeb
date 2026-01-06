<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCustomizeHistory;
use App\Models\FlowerRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CustomizeDeliveryController extends Controller
{

    public function markDelivered(Request $request, $req_id)
    {
        // Authenticated rider
        $rider = Auth::guard('rider-api')->user();

        if (!$rider) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized rider.',
            ], 401);
        }

        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $deliveryStatus = 'delivered';

        // Kolkata current time
        $deliveryTime = Carbon::now('Asia/Kolkata');

        // 1) Find FlowerRequest by request_id (using route req_id)
        $flowerRequest = FlowerRequest::query()
            ->where('request_id', $req_id)
            ->first();

        if (!$flowerRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Customize request not found.',
            ], 404);
        }

        // 2) If already delivered, do not duplicate history
        if (strtolower((string) $flowerRequest->delivery_status) === 'delivered') {

            $lastHistory = DeliveryCustomizeHistory::query()
                ->where('request_id', $flowerRequest->request_id)
                ->latest('id')
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Request is already marked as delivered.',
                'data' => [
                    'request_id' => $flowerRequest->request_id,
                    'delivery_status' => $flowerRequest->delivery_status,
                    'delivered_at' => optional($lastHistory?->delivery_time)->toDateTimeString()
                        ?? optional($lastHistory?->created_at)->toDateTimeString(),
                ],
            ], 200);
        }

        $history = null;

        DB::transaction(function () use ($flowerRequest, $validated, $deliveryStatus, $rider, $deliveryTime, &$history) {

            // A) Update flower_requests delivery_status = delivered
            $flowerRequest->delivery_status = $deliveryStatus;
            $flowerRequest->save();

            // B) Insert delivery history with request_id + rider + location + delivery_time
            $history = DeliveryCustomizeHistory::create([
                'request_id' => $flowerRequest->request_id,
                'rider_id' => $rider->rider_id ?? $rider->id,
                'delivery_status' => $deliveryStatus,
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
                'delivery_time' => $deliveryTime,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Request marked as delivered successfully.',
            'data' => [
                'request_id' => $flowerRequest->request_id,
                'delivery_status' => $flowerRequest->delivery_status,
                'latitude' => $history->latitude,
                'longitude' => $history->longitude,
                'delivered_at' => optional($history->delivery_time)->toDateTimeString()
                    ?? optional($history->created_at)->toDateTimeString(),
            ],
        ], 201);
    }

}