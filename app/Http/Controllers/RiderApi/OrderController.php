<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\DeliveryHistory;
use App\Models\FlowerPickupRequest;

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
                ->where('status', 'pending')
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
                            ->with(['flowerRequest', 'subscription','delivery', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
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


    public function markAsDelivered(Request $request, $order_id)
    {
        try {
            // Authenticate the rider
            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Validate the request for longitude and latitude
            $validated = $request->validate([
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
            ]);

            // Check if the order is assigned to the rider and active
            $order = Order::where('order_id', $order_id)
                        ->where('rider_id', $rider->rider_id)
                        ->whereHas('subscription', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->first();

            if (!$order) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Order not found or not assigned to this rider',
                ], 404);
            }

            // Save delivery history
            $deliveryHistory = DeliveryHistory::create([
                'order_id' => $order->order_id,
                'rider_id' => $rider->rider_id,
                'delivery_status' => 'delivered',
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Order marked as delivered successfully',
                'data' => $deliveryHistory,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while marking the order as delivered.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    //get assign requested orders to rider

    public function getTodayRequestedOrders()
    {
        try {
            // Fetch today's orders based on flower_requests table

            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $today = Carbon::today();
            $orders = Order::whereNotNull('request_id')
                        ->whereHas('flowerRequest', function ($query) use ($today) {
                            $query->whereDate('date', $today);
                        })
                        ->where('rider_id', $rider->rider_id)
                        ->with(['flowerRequest','delivery', 'user', 'flowerProduct', 'address.localityDetails'])
                        ->orderBy('id', 'desc')
                        ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'No requested orders for today',
                    'data' => [],
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Requested orders for today fetched successfully',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching requested orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRequestedDelivered(Request $request, $order_id)
{
    try {
        // Authenticate the rider
        $rider = Auth::guard('rider-api')->user();

        if (!$rider) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Validate the request for longitude and latitude
        $validated = $request->validate([
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        // Check if the requested order is assigned to the rider and has a flower request
        $order = Order::where('order_id', $order_id)
                    ->where('rider_id', $rider->rider_id)
                    ->whereNotNull('request_id')
                    ->whereHas('flowerRequest', function ($query) {
                        $query->whereNotNull('date');
                    })
                    ->first();

        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found, not assigned to this rider, or does not have a valid request',
            ], 404);
        }

        // Save delivery history for requested order
        $deliveryHistory = DeliveryHistory::create([
            'order_id' => $order->order_id,
            'rider_id' => $rider->rider_id,
            'delivery_status' => 'delivered',
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
        ]);

        // Update the order's status to delivered
        $order->update(['status' => 'delivered']);

        return response()->json([
            'status' => 200,
            'message' => 'Requested order marked as delivered successfully',
            'data' => $deliveryHistory,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while marking the requested order as delivered.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function savePickupRequest(Request $request)
{
    // Validate input data
    $request->validate([
        'pickup_date' => 'required|date',
        'pickdetails' => 'required|string',
    ]);

    // Get the authenticated rider
    $rider = Auth::guard('rider-api')->user();
    if (!$rider) {
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    // Create a new flower pickup request
    $pickupRequest = new FlowerPickupRequest();
    $pickupRequest->rider_id = $rider->rider_id;
    $pickupRequest->pickup_date = $request->pickup_date;
    // $pickupRequest->pickdetails = $request->pickdetails;
    $pickupDetails = $request->pickdetails;
    $formattedDetails = collect(explode("\n", $pickupDetails)) // Split by newlines (assuming details are line-separated)
    ->map(fn($detail) => 'Vendor name : other vendor , ' . $detail) // Prepend the text
    ->implode("\n"); // Join back into a single string with newlines

    $pickupRequest->pickdetails = $formattedDetails;
    $pickupRequest->status = 'pending';  // default status is 'pending'
    $pickupRequest->save();

    // Return response
    return response()->json([
        'status' => 200,
        'message' => 'Flower pickup request saved successfully.',
        'data' => $pickupRequest,
    ], 201);
}

}
