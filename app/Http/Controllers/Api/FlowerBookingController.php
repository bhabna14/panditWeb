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
            \Log::info('Purchase subscription called', ['request' => $request->all()]);
            $product = FlowerProduct::findOrFail($request->product_id);
            $user = Auth::guard('sanctum')->user();

            // Generate a unique order ID in the specified format
            $orderId = 'ORD-' . strtoupper(Str::random(12));
            $addressId = $request->address_id;
            // Create the order
            $order = Order::create([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'user_id' => $user->userid,
                'quantity' => 1,
                'total_price' => $product->price,
                'address_id' =>  $addressId,
            ]);

            // Calculate subscription start and end dates
            $startDate = now();
            $endDate = now()->addMonths($product->duration);

            // Create the subscription
            Subscription::create([
                'user_id' => $user->userid,
                'product_id' => $product->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ]);

            // Assuming payment details from Razorpay are provided in $request
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $request->payment_id,
                'user_id' => $user->userid,
                'payment_method' => $request->payment_method,
                'paid_amount' => $product->price,
                'payment_status' => $request->payment_status,
            ]);

            return response()->json([
                'message' => 'Subscription activated successfully',
                'end_date' => $endDate,
                'order_id' => $orderId
            ]);
        }

}
