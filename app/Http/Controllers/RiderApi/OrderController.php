<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getAssignOrders()
    {
        try {
            $rider = Auth::guard('rider-api')->user();
    
            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            // Fetch today's date
            $today = now()->toDateString();
    
            // Fetch orders assigned to the logged-in rider for today
            $orders = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor'])
                ->where('rider_id', $rider->rider_id)
                ->whereDate('pickup_date', $today)
                ->orderBy('created_at', 'desc') // Sort by most recently created first
                ->get();
    
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'No orders assigned for today',
                    'data' => [],
                ]);
            }
    
            return response()->json([
                'status' => 200,
                'message' => 'Assigned orders for today fetched successfully',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function updateFlowerPrices(Request $request, $pickupId)
{
    try {
        
        // Validate the incoming request
        $validated = $request->validate([
            'total_price' => 'required|numeric',
            'flower_pickup_items' => 'required|array',
            'flower_pickup_items.*.flower_id' => 'required|string',
            'flower_pickup_items.*.price' => 'required|numeric',
        ]);

        // Find the pickup record by ID
        $pickup = FlowerPickupDetails::where('pick_up_id', $pickupId)->first();

        if (!$pickup) {
            return response()->json(['message' => 'Pickup not found.'], 404);
        }

        // Update the total price of the pickup
        $pickup->total_price = $validated['total_price'];
        $pickup->status = 'PickupCompleted';
        $pickup->save();

        // Update prices for each flower in flower_pickup_items
        foreach ($validated['flower_pickup_items'] as $item) {
            $flowerPickupItem = FlowerPickupItems::where('pick_up_id', $pickupId)
                ->where('flower_id', $item['flower_id'])
                ->first();

            if ($flowerPickupItem) {
                $flowerPickupItem->price = $item['price'];
                $flowerPickupItem->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Prices updated successfully.'
        ], 200);
    } catch (\Exception $e) {
        // Catch any exception and return an error response
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while updating prices.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
