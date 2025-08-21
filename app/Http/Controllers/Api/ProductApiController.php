<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductOrder;
use App\Models\ProductSucription;
use App\Models\ProductPayment;
use App\Models\ProductRequest;
use App\Models\ProductRequestItem;
use App\Models\Notification;
use App\Mail\FlowerRequestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\SubscriptionConfirmationMail;
use Illuminate\Support\Facades\Mail;

class ProductApiController extends Controller
{

    public function productSubscription(Request $request)
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
            $order = ProductOrder::create([
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
        $today = now()->format('Y-m-d');  // Format the date to match start_date format
    
        // Determine the status based on the start_date
        $status = ($startDate->format('Y-m-d') === $today) ? 'active' : 'pending';
        try {
            ProductSucription::create([
                'subscription_id' => $subscriptionId,
                'user_id' => $user->userid,
                'order_id' => $orderId,
                'product_id' => $productId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'status' => $status 
            ]);
            \Log::info('Subscription created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create subscription'], 500);
        }
    
        // Process payment details and create payment record
        try {
            ProductPayment::create([
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
      // Fetch the complete order details
      $order = ProductOrder::with(['flowerProduct', 'user', 'address.localityDetails', 'flowerPayments', 'subscription'])
      ->where('order_id', $orderId)
      ->first();

        if (!$order) {
        \Log::error('Order not found for email sending');
        return response()->json(['message' => 'Order not found'], 404);
        }

        // Email recipients
        $emails = [
        'bhabana.samantara@33crores.com',
        // 'pankaj.sial@33crores.com',
        // 'basudha@33crores.com',
        // 'priya@33crores.com',
        // 'starleen@33crores.com'
        ];

        // Send the email
        try {
        Mail::to($emails)->send(new SubscriptionConfirmationMail($order));
        \Log::info('Order details email sent successfully', ['emails' => $emails]);
        } catch (\Exception $e) {
        \Log::error('Failed to send order details email', ['error' => $e->getMessage()]);
        }
        return response()->json([
            'message' => 'Subscription activated successfully',
            'end_date' => $endDate,
            'order_id' => $orderId,
        ]);
    }

    public function productRequest(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('sanctum')->user();
            
            // Generate the request_id
            $requestId = 'REQ-' . strtoupper(Str::random(12));
    
            // Create the flower request and store the request_id
            $flowerRequest = ProductRequest::create([
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
                ProductRequestItem::create([
                    'flower_request_id' => $requestId,  // Use the generated request_id
                    'flower_name' => $flowerName,
                    'flower_unit' => $request->flower_unit[$index],
                    'flower_quantity' => $request->flower_quantity[$index],
                ]);
            }
    
            // Eager load the flower_request_items relationship
            // $flowerRequest = $flowerRequest->load('flowerRequestItems');
            $flowerRequest = $flowerRequest->load([
                'order',
                'address.localityDetails', // Load localityDetails as a nested relationship
                'user',
                'flowerProduct',
                'flowerRequestItems',
            ]);
    
            Notification::create([
                'type' => 'order',
                'data' => [
                    'message' => 'A new order has been placed!',
                    'order_id' => $flowerRequest->id,
                    'user_name' => $flowerRequest->user->name, // Assuming the order has a user relation
                ],
                'is_read' => false, // Mark as unread
            ]);
    
            try {
                // Log the alert for a new order
                Log::info('New order created successfully.', ['request_id' => $requestId]);
            
                // Array of email addresses to send the email
                $emails = [
                    'bhabana.samantara@33crores.com',
                    // 'pankaj.sial@33crores.com',
                    // 'basudha@33crores.com',
                    // 'priya@33crores.com',
                    // 'starleen@33crores.com',
                ];
            
                // Log before attempting to send the email
                Log::info('Attempting to send email to multiple recipients.', ['emails' => $emails]);
            
                // Send the email to all recipients
                Mail::to($emails)->send(new FlowerRequestMail($flowerRequest));
            
                // Log success
                Log::info('Email sent successfully to multiple recipients.', [
                    'request_id' => $requestId,
                    'user_id' => $user->userid,
                ]);
            
            } catch (\Exception $e) {
                // Log the error with details
                Log::error('Failed to send email.', [
                    'request_id' => $requestId,
                    'user_id' => $user->userid ?? 'N/A',
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
            
            // Prepare response data including flower details in FlowerRequest
            return response()->json([
                'status' => 200,
                'message' => 'Product request created successfully',
                'data' => $flowerRequest,
            ], 200);
        } catch (\Exception $e) {
            // Log the error and return a response
            Log::error('Failed to create Product request.', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create Product request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function makeRequestPayment(Request $request, $id)
    {
        try {
            // Find the order by flower request ID
            $order = ProductOrder::where('request_id', $id)->firstOrFail();
    
            // Create a new flower payment entry
            ProductPayment::create([
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
 

    
    public function ProductOrdersList()
{
    try {
        
        $userId = Auth::guard('sanctum')->user()->userid;

        $subscriptionsOrder = ProductOrder::whereNull('request_id')
            ->where('user_id', $userId)
            ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($order) {
                if ($order->flowerProduct && $order->flowerProduct->product_image) {
                    $order->flowerProduct->product_image_url = url( $order->flowerProduct->product_image);
                }
                return $order;
            });


        $requestedOrders = ProductRequest::where('user_id', $userId)
            ->with([
                'order' => function ($query) {
                    $query->with('flowerPayments');
                },
                'flowerProduct',
                'user',
                'address.localityDetails',
                'flowerRequestItems',
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($request) {
                if ($request->flowerProduct && $request->flowerProduct->product_image) {
                    $request->flowerProduct->product_image_url = url($request->flowerProduct->product_image);
                }
                return $request;
            });

        return response()->json([
            'success' => 200,
            'data' => [
                'subscriptions_order' => $subscriptionsOrder,
                'requested_orders' => $requestedOrders,
            ],
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Failed to fetch orders list: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve orders list.',
        ], 500);
    }
}

}
