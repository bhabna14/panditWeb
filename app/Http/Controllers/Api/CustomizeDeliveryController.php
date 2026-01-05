<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCustomizeHistory;
use App\Models\FlowerRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomizeDeliveryController extends Controller
{
    public function markDelivered(Request $request, $order_id)
{
    $validated = $request->validate([
        'rider_id'  => ['required'],
        'latitude'  => ['required', 'numeric', 'between:-90,90'],
        'longitude' => ['required', 'numeric', 'between:-180,180'],
        // removed delivery_status from request because this API is only for delivered
    ]);

    // FORCE delivered for this endpoint
    $deliveryStatus = 'delivered';

    // Find Order by order_id
    $order = Order::query()->where('order_id', $order_id)->first();

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Order not found.',
        ], 404);
    }

    // Find related FlowerRequest using order->request_id
    $flowerRequest = FlowerRequest::query()->where('request_id', $order->request_id)->first();

    if (!$flowerRequest) {
        return response()->json([
            'status' => false,
            'message' => 'Customize request not found for this order.',
        ], 404);
    }

    // If already delivered, return without duplicating history (idempotent)
    if (strtolower((string) $flowerRequest->delivery_status) === 'delivered') {
        return response()->json([
            'status' => true,
            'message' => 'Order is already marked as delivered.',
            'data' => [
                'order_id' => $order->order_id,
                'request_id' => $flowerRequest->request_id,
                'delivery_status' => $flowerRequest->delivery_status,
            ],
        ], 200);
    }

    $history = null;

    DB::transaction(function () use ($flowerRequest, $order, $validated, $deliveryStatus, &$history) {

        // 1) Update FlowerRequest delivery_status to delivered (FORCED)
        $flowerRequest->delivery_status = $deliveryStatus;
        $flowerRequest->save();

        // 2) Create Delivery history with location
        $history = DeliveryCustomizeHistory::create([
            'order_id' => $order->order_id,
            'rider_id' => $validated['rider_id'],
            'delivery_status' => $deliveryStatus,
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
        ]);

        // OPTIONAL: if orders table has delivery_status column
        // $order->delivery_status = $deliveryStatus;
        // $order->save();
    });

    return response()->json([
        'status' => true,
        'message' => 'Order marked as delivered successfully.',
        'data' => [
            'order_id' => $order->order_id,
            'request_id' => $flowerRequest->request_id,
            'delivery_status' => $flowerRequest->delivery_status,
            'latitude' => $history->latitude,
            'longitude' => $history->longitude,
            'delivered_at' => optional($history->created_at)->toDateTimeString(),
        ],
    ], 201);
}

}
