<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    // assign pickup details to rider
    public function getAssignPickup()
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
                    'message' => 'No pickups assigned for today',
                    'data' => [],
                ]);
            }
    
            return response()->json([
                'status' => 200,
                'message' => 'Assigned pickups for today fetched successfully',
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
    
    // update price of each item of pickup by Rider
    public function updateFlowerPrices(Request $request, $pickupId)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'total_price' => 'required|numeric',
                'flower_pickup_items' => 'required|array',
                'flower_pickup_items.*.flower_id' => 'required|string',
                'flower_pickup_items.*.price' => 'required|numeric', // Ensure correct price validation
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
                    // Update the flower price
                    $flowerPickupItem->price = $item['price'];
                    $flowerPickupItem->save();
                }
            }
    
            return response()->json([
                'status' => 200,
                'message' => 'Prices updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    //get assign order to rider
    public function getAssignedOrders()
{
    try {
        // Check if the rider is authenticated
        $rider = Auth::guard('rider-api')->user();

        if (!$rider) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Fetch active orders assigned to the rider
        $orders = Order::where('rider_id', $rider->rider_id)
                        ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                        ->whereHas('subscription', function($query) {
                            // Only fetch orders where the subscription is active
                            $query->where('status', 'active');
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        // Check if the orders collection is empty
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'No orders assigned for today',
                'data' => [],
            ]);
        }

        // Return the assigned orders if found
        return response()->json([
            'status' => 200,
            'message' => 'Assigned orders fetched successfully',
            'data' => $orders,
        ]);
        
    } catch (\Exception $e) {
        // Handle any exceptions and return a 500 server error response
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while fetching orders.',
            'error' => $e->getMessage(), // Optionally, you can log this error for debugging
        ], 500);
    }
}


}
