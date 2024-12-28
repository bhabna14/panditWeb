<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\DeliveryHistory;
use App\Models\FlowerPickupRequest;
use App\Models\DeliveryStartHistory;

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
                'flower_pickup_items.*.id' => 'required|integer', // Use unique identifier if available
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
                // Use the unique 'id' to update the correct record
                $flowerPickupItem = FlowerPickupItems::where('id', $item['id'])->first();
    
                if ($flowerPickupItem) {
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
    

    // startDelivery method to start delivery for rider
    public function startDelivery(Request $request)
    {
        try {
            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Fetch today's orders assigned to the rider
            $today = Carbon::today();
            $orders = Order::where('rider_id', $rider->rider_id)
                ->whereHas('subscription', function ($query) use ($today) {
                    $query->where('status', 'active')
                        ->whereDate('end_date', '>=', $today);
                })
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'No orders assigned for today',
                    'data' => [],
                ]);
            }

            // Save orders to delivery_history
            foreach ($orders as $order) {
                DeliveryHistory::create([
                    'order_id' => $order->order_id,
                    'rider_id' => $rider->rider_id,
                    'delivery_status' => 'pending',
                    'start_delivery_time' => now(),
                    'longitude' => $request->longitude ?? null, // Optional if provided
                    'latitude' => $request->latitude ?? null,   // Optional if provided
                ]);
            }
                // Also save delivery start time in the new table
                DeliveryStartHistory::create([
                    'rider_id' => $rider->rider_id,
                    'start_delivery_time' => now(), // Save the current time as start delivery time
                ]);
            return response()->json([
                'status' => 200,
                'message' => 'Delivery started successfully. Orders have been saved in delivery history.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while starting the delivery.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    //get assign order to rider every day basis till the end date and subscription is status is active
    // public function getAssignedOrders()
    // {
    //     try {
    //         // Fetch today's orders based on subscription table
    
    //         $rider = Auth::guard('rider-api')->user();
    
    //         if (!$rider) {
    //             return response()->json([
    //                 'status' => 401,
    //                 'message' => 'Unauthorized',
    //             ], 401);
    //         }
    
    //         $today = Carbon::today();
    //         $orders = Order::where('rider_id', $rider->rider_id)
            
    //             ->whereHas('subscription', function ($query) use ($today) {
    //                 $query->where('status', 'active')
    //                 ->whereDate('end_date', '>=', $today);
    //             })
    //             ->with(['subscription', 'delivery', 'user', 'flowerProduct', 'address.localityDetails'])
    //             ->orderBy('id', 'desc')
    //             ->get();
    
    //         if ($orders->isEmpty()) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'No orders assigned for today',
    //                 'data' => [],
    //             ]);
    //         }
    
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Assigned orders for today fetched successfully',
    //             'data' => $orders,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'An error occurred while fetching assigned orders.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function getAssignedOrders()
    {
        try {
            // Fetch today's orders based on subscription table
            $rider = Auth::guard('rider-api')->user();
    
            if (!$rider) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            $today = Carbon::today();
    
            // Join delivery_history and delivery_start_histories and apply conditions
            $orders = Order::select('orders.*')
                ->join('delivery_history', 'orders.order_id', '=', 'delivery_history.order_id')
                ->leftJoin('delivery_start_histories', 'delivery_history.rider_id', '=', 'delivery_start_histories.rider_id')
                ->where('delivery_history.delivery_status', 'pending')
                ->whereDate('delivery_start_histories.start_delivery_time', $today)
                ->where('delivery_history.rider_id', $rider->rider_id)
                ->whereHas('subscription', function ($query) use ($today) {
                    $query->where('status', 'active')
                        ->whereDate('end_date', '>=', $today);
                })
                ->with(['subscription', 'delivery', 'user', 'flowerProduct', 'address.localityDetails']) // Eager load the related models
                ->orderBy('orders.id', 'desc')
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
                'message' => 'An error occurred while fetching assigned orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    



    // Mark order as delivered by rider
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
