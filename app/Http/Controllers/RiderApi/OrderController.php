<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\DeliveryHistory;
use App\Models\Order;;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
    
            // Fetch active subscription-based orders assigned to the rider
            $subscriptionOrders = Order::where('rider_id', $rider->rider_id)
                ->with(['flowerRequest', 'subscription', 'delivery', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                ->whereHas('subscription', function ($query) {
                    $query->where('status', 'active');
                })
                ->orderBy('id', 'desc')
                ->get();
    
            // Fetch today's requested orders
            $today = Carbon::today();
            $requestedOrders = Order::whereNotNull('request_id')
                ->where('rider_id', $rider->rider_id)
                ->with(['flowerRequest', 'subscription', 'delivery', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                ->whereHas('flowerRequest', function ($query) use ($today) {
                    $query->whereDate('date', $today);
                })
                ->orderBy('id', 'desc')
                ->get();
    
            // Combine both subscription-based orders and today's requested orders
            $allOrders = $subscriptionOrders->merge($requestedOrders);
    
            // Check if any orders are found
            if ($allOrders->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'No orders assigned for today',
                    'data' => [],
                ]);
            }
    
            // Return the combined orders
            return response()->json([
                'status' => 200,
                'message' => 'Assigned orders fetched successfully',
                'data' => $allOrders,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions and return a 500 server error response
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


    // Import the Log facade

    // use Illuminate\Support\Facades\Log;

    public function markAsDelivered(Request $request, $order_id)
    {
        try {
            // Authenticate the rider
            $rider = Auth::guard('rider-api')->user();
    
            if (!$rider) {
                Log::info('Rider authentication failed', ['rider_id' => $request->rider_id]);
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            // Log rider authentication attempt
            Log::info('Rider authenticated', ['rider_id' => $rider->rider_id]);
    
            // Validate the request for longitude and latitude
            $validated = $request->validate([
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
            ]);
    
            // Log location validation
            Log::info('Delivery location validated', [
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
            ]);
    
            // Set the date for flower request filtering
            $today = now()->toDateString();
    
            // Log the date being used for the flower request
            Log::info('Today\'s date for flower request', ['date' => $today]);
    
            // Separate order query components
            $orderQuery = Order::where('order_id', $order_id)
                                ->where('rider_id', $rider->rider_id)
                                ->whereNotNull('request_id');
    
            // Subscription filter condition
            $subscriptionCondition = function ($query) {
                $query->where('status', 'active');
            };
    
            // FlowerRequest filter for today's date
            $flowerRequestCondition = function ($query) use ($today) {
                $query->whereDate('date', $today);
            };
    
            // Relationships to load
            $relationships = ['flowerRequest', 'subscription', 'delivery', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'];
    
            // Execute the query by merging the conditions
            $order = $orderQuery->whereHas('subscription', $subscriptionCondition)
                                ->whereHas('flowerRequest', $flowerRequestCondition)
                                ->with($relationships)
                                ->orderBy('id', 'desc')
                                ->first();
    
            // Log the query result
            Log::info('Order fetch attempt', ['order_id' => $order_id, 'rider_id' => $rider->rider_id, 'order_found' => $order ? true : false]);
    
            if (!$order) {
                // Log when the order is not found
                Log::warning('Order not found or not assigned to rider', [
                    'order_id' => $order_id,
                    'rider_id' => $rider->rider_id
                ]);
                return response()->json([
                    'status' => 404,
                    'message' => 'Order not found or not assigned to this rider',
                ], 404);
            }
    
            // Save delivery history
            $deliveryHistory = DeliveryHistory::create([
                'order_id' => $order->order_id,
                'rider_id' => $rider->rider_id,
                'delivery_status' => 'delivered',  // You can set this as needed
                'longitude' => $validated['longitude'],  // Assuming validated data
                'latitude' => $validated['latitude'],  // Assuming validated data
            ]);
    
            // Log delivery history creation
            Log::info('Delivery history saved', [
                'order_id' => $order->order_id,
                'rider_id' => $rider->rider_id,
                'delivery_status' => 'delivered',
            ]);
    
            // Return success response
            return response()->json([
                'status' => 200,
                'message' => 'Order marked as delivered successfully',
                'data' => $deliveryHistory,
            ]);
    
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error occurred while marking the order as delivered', [
                'order_id' => $order_id,
                'rider_id' => $rider->rider_id,
                'error_message' => $e->getMessage(),
            ]);
    
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while marking the order as delivered.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    

}
