<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;

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
    

    public function submitPickupPrice(Request $request, $id)
    {
        $rider = Auth::guard('rider-api')->user();

        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        // Find the pickup assigned to the logged-in rider
        $pickup = FlowerPickupDetails::where('id', $id)
            ->where('rider_id', $rider->rider_id)
            ->first();

        // If not found, return an error
        if (!$pickup) {
            return response()->json(['error' => 'Pickup not found or not assigned to you.'], 404);
        }

        // Update the price and status
        $pickup->update([
            'price' => $request->price,
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Pickup price submitted successfully.']);
    }
}
