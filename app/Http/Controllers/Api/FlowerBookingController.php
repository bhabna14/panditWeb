<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Auth;

use App\Models\FlowerPayment;
class FlowerBookingController extends Controller
{
    //
   

    public function purchaseSubscription(Request $request)
    {
        // Log the incoming request data
        \Log::info('Purchase subscription called', ['request' => $request->all()]);
    
        // Directly fetch the product without validation
        $product = FlowerProduct::find($request->product_id); // Use find() instead of findOrFail()
        if (!$product) {
            \Log::warning('Product not found, proceeding with null value', ['product_id' => $request->product_id]);
        } else {
            \Log::info('Product found', ['product' => $product]);
        }
    
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            \Log::error('User not authenticated');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Generate a unique order ID in the specified format
        $orderId = 'ORD-' . strtoupper(Str::random(12));
        $addressId = $request->address_id;
    
        // Log the order creation attempt
        \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $product ? $product->id : null, 'user_id' => $user->userid, 'address_id' => $addressId]);
    
        // Create the order
        try {
            $order = Order::create([
                'order_id' => $orderId,
                'product_id' => $product ? $product->id : null, // Use null if product not found
                'user_id' => $user->userid,
                'quantity' => 1,
                'total_price' => $product ? $product->price : 0, // Set total_price to 0 if product not found
                'address_id' => $addressId,
            ]);
            \Log::info('Order created successfully', ['order' => $order]);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    
        // Calculate subscription start and end dates
        $startDate = now();
        $endDate = now()->addMonths($product ? $product->duration : 0); // Default duration to 0 if product not found
    
        // Log subscription creation
        \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $product ? $product->id : null, 'start_date' => $startDate, 'end_date' => $endDate]);
    
        // Create the subscription without validation
        try {
            Subscription::create([
                'user_id' => $user->userid,
                'product_id' => $product ? $product->product_id : null, // Use null if product not found
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
            'payment_method' => $request->payment_method,
            'paid_amount' => $product ? $product->price : 0, // Default to 0 if product not found
            'payment_status' => $request->payment_status,
        ]);
    
        // Create the payment record
        try {
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $request->payment_id,
                'user_id' => $user->userid,
                'payment_method' => $request->payment_method,
                'paid_amount' => $product ? $product->price : 0, // Default to 0 if product not found
                'payment_status' => $request->payment_status,
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
    
}
