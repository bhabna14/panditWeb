<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Models\FlowerRequest;

use Illuminate\Support\Facades\Auth;

use App\Models\FlowerPayment;
class FlowerBookingController extends Controller
{
    //
   

    public function purchaseSubscription(Request $request)
    {
        // Log the incoming request data
        \Log::info('Purchase subscription called', ['request' => $request->all()]);
    
        // Extract the product_id from the request
        $productId = $request->product_id; // Use the product_id directly from the request
        $user = Auth::guard('sanctum')->user();
    
        // Check if the user is authenticated
        if (!$user) {
            \Log::error('User not authenticated');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Generate a unique order ID in the specified format
        $orderId = 'ORD-' . strtoupper(Str::random(12));
        $addressId = $request->address_id;
        $suggestion = $request->suggestion;
    
        // Log the order creation attempt
        \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);
    
        // Create the order
        try {
            $order = Order::create([
                'order_id' => $orderId,
                'product_id' => $productId, // Store product_id as provided in the request
                'user_id' => $user->userid,
                'quantity' => 1,
                'total_price' => $request->paid_amount, // Use the paid amount directly from the request
                'address_id' => $addressId,
                'suggestion' => $suggestion,
            ]);
            \Log::info('Order created successfully', ['order' => $order]);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    
        // Calculate subscription start and end dates
        $startDate = now();
        $endDate = now()->addMonths(1); // Assuming a default duration of 1 month for simplicity; adjust as needed
    
        // Log subscription creation
        \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);
    
        // Create the subscription without validation
        try {
            Subscription::create([
                'user_id' => $user->userid,
                'order_id' => $orderId,
                'product_id' => $productId, // Store product_id as provided in the request
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ]);
            \Log::info('Subscription created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create subscription'], 500);
        }
    
        // Log payment details
        \Log::info('Processing payment', [
            'order_id' => $orderId,
            'payment_id' => $request->payment_id,
            'user_id' => $user->userid,
            'payment_method' => "Razorpay",
            'paid_amount' => $request->paid_amount, // Use the paid amount directly from the request
            'payment_status' => "paid",
        ]);
    
        // Create the payment record
        try {
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $request->payment_id,
                'user_id' => $user->userid,
                'payment_method' => "Razorpay",
                'paid_amount' => $request->paid_amount, // Use the paid amount directly from the request
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
            // Validate the incoming request
          
            // Get the authenticated user
            $user = Auth::guard('sanctum')->user();
            $requestId = 'REQ-' . strtoupper(Str::random(12));
            // Create the flower request
            $flowerRequest = FlowerRequest::create([
                'request_id' => $requestId,
                'product_id' => $request->product_id,
                'user_id' => $user->userid,
                'address_id' => $request->address_id,
                'description' => $request->description,
                'suggestion' => $request->suggestion,
                'status' => 'pending'
            ]);
    
            return response()->json([
                'message' => 'Flower request created successfully',
                'data' => $flowerRequest,
            ], 201);
        } catch (\Exception $e) {
            // Log the exception
            \Log::error('Error creating flower request: ' . $e->getMessage());
    
            return response()->json([
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
        ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address'])
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
            ->with(['order' => function ($query) {
                $query->with(['flowerPayments']);
            }])
            ->get();


   
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

}
