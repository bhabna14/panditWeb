<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Models\FlowerRequest;
use App\Models\SubscriptionPauseResumeLog;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade

use Illuminate\Support\Facades\Auth;

use App\Models\FlowerPayment;
use App\Models\FlowerRequestItem;


class FlowerBookingController extends Controller
{
    //
   

    public function purchaseSubscription(Request $request)
    {
        // Log the incoming request data
        \Log::info('Purchase subscription called', ['request' => $request->all()]);
    
        // Extract the product_id and other necessary fields from the request
        $productId = $request->product_id; 
        $user = Auth::guard('sanctum')->user();
    
        // Check if the user is authenticated
        if (!$user) {
            \Log::error('User not authenticated');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Generate a unique order ID
        $orderId = 'ORD-' . strtoupper(Str::random(12));
        $addressId = $request->address_id;
        $suggestion = $request->suggestion;
    
        // Log the order creation attempt
        \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);
    
        // Create the order
        try {
            $order = Order::create([
                'order_id' => $orderId,
                'product_id' => $productId, 
                'user_id' => $user->userid,
                'quantity' => 1,
                'total_price' => $request->paid_amount,
                'address_id' => $addressId,
                'suggestion' => $suggestion,
            ]);
            \Log::info('Order created successfully', ['order' => $order]);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    
        // Calculate subscription start and end dates
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now(); // Default to now if no start date is provided
        $duration = $request->duration; // Duration is 1 for 30 days, 3 for 60 days, 6 for 90 days
        
        // Calculate end date based on subscription duration
        if ($duration == 1) {
            $endDate = $startDate->copy()->addDays(29); // For 1, add 30 days
        } else if ($duration == 3) {
            $endDate = $startDate->copy()->addDays(89); // For 3, add 90 days
        } else if ($duration == 6) {
            $endDate = $startDate->copy()->addDays(179); // For 6, add 180 days
        }
        else {
            // Handle unexpected duration value
            \Log::error('Invalid subscription duration', ['duration' => $duration]);
            return response()->json(['message' => 'Invalid subscription duration'], 400);
        }
        
    
        // Log subscription creation
        \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);
    
        // Create the subscription
        $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
        try {
            Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id' => $user->userid,
                'order_id' => $orderId,
                'product_id' => $productId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'status' => 'active'
            ]);
            \Log::info('Subscription created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create subscription'], 500);
        }
    
        // Process payment details and create payment record
        try {
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $request->payment_id,
                'user_id' => $user->userid,
                'payment_method' => "Razorpay",
                'paid_amount' => $request->paid_amount,
                'payment_status' => "paid",
            ]);
            \Log::info('Payment recorded successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to record payment', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to record payment'], 500);
        }
    
        return response()->json([
            'message' => 'Subscription activated successfully',
            'end_date' => $endDate,
            'order_id' => $orderId,
        ]);
    }
    
    public function storerequest(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('sanctum')->user();
            
            // Generate the request_id
            $requestId = 'REQ-' . strtoupper(Str::random(12));
    
            // Create the flower request and store the request_id
            $flowerRequest = FlowerRequest::create([
                'request_id' => $requestId,  // Store request_id in FlowerRequest
                'product_id' => $request->product_id,
                'user_id' => $user->userid,
                'address_id' => $request->address_id,
                'description' => $request->description,
                'suggestion' => $request->suggestion,
                'date' => $request->date,
                'time' => $request->time,
                'status' => 'pending'
            ]);
    
            // Loop through flower names, units, and quantities to create FlowerRequestItem entries
            foreach ($request->flower_name as $index => $flowerName) {
                // Create a FlowerRequestItem with flower_request_id set to the generated request_id
                FlowerRequestItem::create([
                    'flower_request_id' => $requestId,  // Use the generated request_id
                    'flower_name' => $flowerName,
                    'flower_unit' => $request->flower_unit[$index],
                    'flower_quantity' => $request->flower_quantity[$index],
                ]);
            }
    
            // Eager load the flower_request_items relationship
            $flowerRequest = $flowerRequest->load('flowerRequestItems');
    
            // Prepare response data including flower details in FlowerRequest
            return response()->json([
                'status' => 200,
                'message' => 'Flower request created successfully',
                'data' => $flowerRequest,
            ], 200);
        } catch (\Exception $e) {
          
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create flower request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function ordersList()
    {
        try {
            // Get the authenticated user's ID
            $userId = Auth::guard('sanctum')->user()->userid;

            // Fetch standalone orders for the authenticated user (orders without request_id)
            $subscriptionsOrder = Order::whereNull('request_id')
            ->where('user_id', $userId)
            ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails','pauseResumeLogs'])
            ->orderBy('id', 'desc')
            ->get();
        
        // Map to add the product_image_url to each order's flowerProduct
        $subscriptionsOrder = $subscriptionsOrder->map(function ($order) {
            if ($order->flowerProduct) {
                // Ensure flowerProduct exists before accessing product_image
                $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image); // Generate full URL for the photo
            }
            return $order;
        });
        

            // Fetch related orders for the authenticated user (orders with request_id)
            $requestedOrders = FlowerRequest::where('user_id', $userId)
            ->with([
                'order' => function ($query) {
                    $query->with('flowerPayments');
                },
                'flowerProduct',
                'user',
                'address.localityDetails',
                'flowerRequestItems' 
            ])
            ->orderBy('id', 'desc')
            ->get()
            // ->orderBy('id', 'desc')
            ->map(function ($request) {
                // Check if 'order' relationship exists and has 'flower_payments'
                if ($request->order) {
                    // If 'flower_payments' is empty, set it to an empty object
                    if ($request->order->flowerPayments->isEmpty()) {
                        $request->order->flower_payments = (object)[];
                    } else {
                        // Otherwise, assign the 'flowerPayments' collection to 'flower_payments'
                        $request->order->flower_payments = $request->order->flowerPayments;
                    }
                    // Remove the 'flowerPayments' property to avoid duplication
                    unset($request->order->flowerPayments);
                }
        
                // Map product image URL
                if ($request->flowerProduct) {
                    // Generate full URL for the product image
                    $request->flowerProduct->product_image_url = asset('storage/' . $request->flowerProduct->product_image);
                }
        
                return $request;
            });
        
        


    
            // Combine both into a single response
            return response()->json([
                'success' => 200,
                'data' => [
                    'subscriptions_order' => $subscriptionsOrder,
                    'requested_orders' => $requestedOrders,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to fetch orders list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders list.',
            ], 500);
        }
    }

    
//     public function ordersList()
// {
//     try {
//         // Fetch the user ID
//         $userId = Auth::guard('sanctum')->user()->userid;

//         // Get both subscription and request-based orders in a single query
//         $mergedOrdersObject = $this->getUserOrders($userId);

//         // Return response with the merged orders under the 'date' key
//         return response()->json([
//             'success' => 200,
//             'data' => $mergedOrdersObject
//         ]);
//     } catch (\Exception $e) {
//         // Handle errors gracefully
//         return response()->json([
//             'error' => 'Something went wrong',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }
// public function getUserOrders($userId)
// {
//     // Fetch subscription orders with relations
//     $subscriptionsOrder = Order::whereNull('request_id')
//         ->where('user_id', $userId)
//         ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])
//         ->orderBy('id', 'desc')
//         ->get();
//     $subscriptionsOrder->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });
//     // Fetch request-based orders with relations
//     $requestedOrders = FlowerRequest::where('user_id', $userId)
//         ->with(['order', 'flowerProduct', 'user', 'address'])
//         ->orderBy('id', 'desc')
//         ->get();
//         $requestedOrders->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Merge both sets of orders and reset the keys to ensure the response is an array
//     return  $requestedOrders->merge($subscriptionsOrder)->sortByDesc('id')->values()->toArray();
    
// }

// private function getUserOrders($userId)
// {
//     // Fetch subscription orders with relations
//     $subscriptionsOrder = Order::whereNull('request_id')
//         ->where('user_id', $userId)
//         ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])
//         ->orderBy('id', 'desc')
//         ->get();

//     // Loop through the subscription orders and generate the full URL for flowerProduct->product_image
//     $subscriptionsOrder->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Fetch request-based orders with relations
//     $requestedOrders = FlowerRequest::where('user_id', $userId)
//         ->with(['order', 'flowerProduct', 'user', 'address'])
//         ->orderBy('id', 'desc')
//         ->get();

//     // Loop through the request-based orders and generate the full URL for flowerProduct->product_image
//     $requestedOrders->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Merge both sets of orders
//     return $subscriptionsOrder->merge($requestedOrders)->sortByDesc('id');
// }


  
     // old code
    // public function pause(Request $request, $order_id)
    // {
    //     try {
    //         // Find the subscription by order_id
    //         $subscription = Subscription::where('order_id', $order_id)->firstOrFail();
            
    //         // Log the subscription being paused
    //         Log::info('Pausing subscription', [
    //             'order_id' => $order_id,
    //             'user_id' => $subscription->user_id,
    //             'pause_start_date' => $request->pause_start_date,
    //             'pause_end_date' => $request->pause_end_date,
    //         ]);
        
    //         // Calculate the number of days to extend (include both start and end date)
    //         $pauseStartDate = Carbon::parse($request->pause_start_date);
    //         $pauseEndDate = Carbon::parse($request->pause_end_date);
    //         $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates
        
    //         // Store the new paused end date in the new_date field
    //         $newEndDate = Carbon::parse($subscription->end_date)->addDays($pausedDays);
    
    //         // Update the subscription status and dates
    //         $subscription->status = 'paused';
    //         $subscription->pause_start_date = $pauseStartDate;
    //         $subscription->pause_end_date = $pauseEndDate;
    //         $subscription->new_date = $newEndDate; // Store the new end date after pausing
    //         $subscription->is_active = true;
        
    //         // Save the changes
    //         $subscription->save();
        
    //         // Log the successful pause
    //         Log::info('Subscription paused successfully', [
    //             'order_id' => $order_id,
    //             'new_end_date' => $newEndDate,
    //         ]);
        
    //         // Log the pause action
    //         SubscriptionPauseResumeLog::create([
    //             'subscription_id' => $subscription->subscription_id,
    //             'order_id' => $order_id,
    //             'action' => 'paused',
    //             'pause_start_date' => $pauseStartDate,
    //             'pause_end_date' => $pauseEndDate,
    //             'paused_days' => $pausedDays,
    //             'new_end_date' => $subscription->new_date,

    //         ]);
        
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Subscription paused successfully.',
    //             'subscription' => $subscription
    //         ], 200);    
    //     } catch (\Exception $e) {
    //         // Log any errors that occur during the process
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error_message' => $e->getMessage(),
    //         ]);
        
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while pausing the subscription.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function pause(Request $request, $order_id)
    {
        try {
           
            // Find the subscription by order_id
            $subscription = Subscription::where('order_id', $order_id)->firstOrFail();
          
            // Calculate pause start and end dates
            $pauseStartDate = Carbon::parse($request->pause_start_date);
            $pauseEndDate = Carbon::parse($request->pause_end_date);
            $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates
    
           
            // Get the most recent new_end_date or default to the original end_date
            $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                ->orderBy('id', 'desc')
                ->value('new_end_date');
    
            // Use the most recent new_end_date for recalculating the new end date
            $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);
    
           
            // Calculate the new end date by adding paused days
            $newEndDate = $currentEndDate->addDays($pausedDays);
    
         
            // Update the subscription status and new date field
            $subscription->status = 'paused';
            $subscription->pause_start_date = $pauseStartDate;
            $subscription->pause_end_date = $pauseEndDate;
            $subscription->new_date = $newEndDate; // Update with recalculated end date
            $subscription->is_active = true;
    
            // Save the changes
            $subscription->save();
    
            // Log the pause action
            SubscriptionPauseResumeLog::create([
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $order_id,
                'action' => 'paused',
                'pause_start_date' => $pauseStartDate,
                'pause_end_date' => $pauseEndDate,
                'paused_days' => $pausedDays,
                'new_end_date' => $newEndDate,
            ]);
    
            // Log the creation of the pause resume log
            Log::info('Pause resume log created successfully');
    
            return response()->json([
                'success' => 200,
                'message' => 'Subscription paused successfully.',
                'subscription' => $subscription
            ], 200);    
        } catch (\Exception $e) {
            // Log any errors that occur during the process
            Log::error('Error pausing subscription', [
                'order_id' => $order_id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => 500,
                'message' => 'An error occurred while pausing the subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    
    
 
    public function markPaymentApi(Request $request, $id)
{
    try {
        // Find the order by flower request ID
        $order = Order::where('request_id', $id)->firstOrFail();

        // Create a new flower payment entry
        FlowerPayment::create([
            'order_id' => $order->order_id,
            'payment_id' => $request->payment_id, // Can be set later if available
            'user_id' => $order->user_id,
            'payment_method' => 'Razorpay',
            'paid_amount' => $order->total_price,
            'payment_status' => 'paid',
        ]);

        // Update the status of the FlowerRequest to "paid"
        $flowerRequest = FlowerRequest::where('request_id', $id)->firstOrFail();

        if ($flowerRequest->status === 'approved') {
            $flowerRequest->status = 'paid';
            $flowerRequest->save();
        }
        

        return response()->json([
            'status' => 200,
            'message' => 'Payment marked as paid'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Failed to mark payment as paid'
        ], 500);
    }
}
// public function resume(Request $request, $order_id)
// {
//     try {
//         // Find the subscription by order_id
//         $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

//         // Validate that the subscription is currently paused
//         if ($subscription->status !== 'paused') {
//             return response()->json([
//                 'success' => 400,
//                 'message' => 'Subscription is not in a paused state.'
//             ], 400);
//         }

//         // Log the resume attempt
//         Log::info('Resuming subscription', [
//             'order_id' => $order_id,
//             'user_id' => $subscription->user_id,
//             'pause_start_date' => $subscription->pause_start_date,
//             'pause_end_date' => $subscription->pause_end_date,
//         ]);

//         // Parse the dates
//         $resumeDate = Carbon::parse($request->resume_date);
//         $pauseStartDate = Carbon::parse($subscription->pause_start_date);
//         $startDate = Carbon::parse($subscription->end_date);

//         // Ensure the resume date is within the pause period
//         if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt(Carbon::parse($subscription->pause_end_date))) {
//             return response()->json([
//                 'success' => 400,
//                 'message' => 'Resume date must be within the pause period.'
//             ], 400);
//         }

//         // Calculate the days paused up to the resume date
//         $pausedDays = $resumeDate->diffInDays($pauseStartDate) + 1; // Include the start date

//         // Calculate the new end date
//         $newEndDate = $startDate->addDays($pausedDays);

//         // Update the subscription status and add resume_date
//         $subscription->status = 'active';
//         $subscription->pause_start_date = null;
//         $subscription->pause_end_date = null;
//         // $subscription->resume_date = $resumeDate; // Add the resume date
//         $subscription->new_date = $newEndDate;
//         $subscription->save();

//         // Log the resume action
//         SubscriptionPauseResumeLog::create([
//             'subscription_id' => $subscription->subscription_id,
//             'order_id' => $order_id,
//             'action' => 'resumed',
//             'pause_start_date' => $pauseStartDate,
//             'resume_date' => $resumeDate, // Log the resume date
//             'new_end_date' => $newEndDate,
//             'paused_days' => $pausedDays,
//         ]);

//         // Log the successful resume
//         Log::info('Subscription resumed successfully', [
//             'order_id' => $order_id,
//             'new_end_date' => $newEndDate,
//         ]);

//         return response()->json([
//             'success' => 200,
//             'message' => 'Subscription resumed successfully.',
//             'subscription' => $subscription
//         ], 200);
//     } catch (\Exception $e) {
//         // Log any errors that occur during the process
//         Log::error('Error resuming subscription', [
//             'order_id' => $order_id,
//             'error_message' => $e->getMessage(),
//         ]);

//         return response()->json([
//             'success' => 500,
//             'message' => 'An error occurred while resuming the subscription.',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
public function resume(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

        // Validate that the subscription is currently paused
        if ($subscription->status !== 'paused') {
            return response()->json([
                'success' => 400,
                'message' => 'Subscription is not in a paused state.'
            ], 400);
        }

        // Log the resume attempt
        Log::info('Resuming subscription', [
            'order_id' => $order_id,
            'user_id' => $subscription->user_id,
            'pause_start_date' => $subscription->pause_start_date,
            'pause_end_date' => $subscription->pause_end_date,
        ]);

        // Parse the dates
        $resumeDate = Carbon::parse($request->resume_date);
        $pauseStartDate = Carbon::parse($subscription->pause_start_date);
        $pauseEndDate = Carbon::parse($subscription->pause_end_date);
        $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);

        // Ensure the resume date is within the pause period
        if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
            return response()->json([
                'success' => 400,
                'message' => 'Resume date must be within the pause period.'
            ], 400);
        }

        // Calculate the days actually paused until the resume date
        $actualPausedDays = $resumeDate->diffInDays($pauseStartDate); // Include start date

        // Calculate total planned paused days
        $totalPausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1;

        // Calculate the remaining paused days to adjust if resuming early
        $remainingPausedDays = $totalPausedDays - $actualPausedDays;

        // Adjust the new end date by subtracting the remaining paused days if necessary
        if ($remainingPausedDays > 0) {
            $newEndDate = $currentEndDate->subDays($actualPausedDays);
        } else {
            $newEndDate = $currentEndDate;
        }

        // Update the subscription status and clear pause dates
        $subscription->status = 'active';
        $subscription->pause_start_date = null;
        $subscription->pause_end_date = null;
        $subscription->new_date = $newEndDate;
        $subscription->save();

        // Log the resume action 
        SubscriptionPauseResumeLog::create([
            'subscription_id' => $subscription->subscription_id,
            'order_id' => $order_id,
            'action' => 'resumed',
            'resume_date' => $resumeDate,
            'pause_start_date' => $pauseStartDate,
            'new_end_date' => $newEndDate,
            'paused_days' => $actualPausedDays,
        ]);

        // Log the successful resume
        Log::info('Subscription resumed successfully', [
            'order_id' => $order_id,
            'new_end_date' => $newEndDate,
        ]);

        return response()->json([
            'success' => 200,
            'message' => 'Subscription resumed successfully.',
            'subscription' => $subscription
        ], 200);
    } catch (\Exception $e) {
        // Log any errors that occur during the process
        Log::error('Error resuming subscription', [
            'order_id' => $order_id,
            'error_message' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => 500,
            'message' => 'An error occurred while resuming the subscription.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
