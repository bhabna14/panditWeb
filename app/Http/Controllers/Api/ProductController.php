<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    //
    public function getActiveProducts()
{
    $products = FlowerProduct::where('status', 'active')
    ->get();

    return response()->json([
        'status' => 200,
        'message' => 'Products retrieved successfully.',
        'data' => $products
    ], 200);
}

public function getCurrentOrders(Request $request)
{          

    try {
        if (Auth::guard('sanctum')->check()) {

            $userId = Auth::guard('sanctum')->user()->userid;
        
            // Fetch current orders with the latest subscription for each order_id
            $currentOrders = Order::whereNull('request_id')
                ->where('user_id', $userId)
                ->whereHas('subscription', function ($query) {
                    $query->where('status', '!=', 'dead');
                })
                ->with([
                    'subscription' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    },
                    'flowerPayments',
                    'user',
                    'flowerProduct',
                    'address.localityDetails',
                    'pauseResumeLogs',
                ])
                ->orderBy('id', 'asc')
                ->get();
        
            // Process the orders and subscriptions
            $currentOrders = $currentOrders->map(function ($order) {
                $subscription = $order->subscription;
                if ($subscription) {
                    $subscription->display_end_date = $subscription->new_date ?? $subscription->end_date;
                    $subscription->remaining_time = now()->diff($subscription->display_end_date);
                }
        
                if ($order->flowerProduct) {
                    $order->flowerProduct->product_image_url = $order->flowerProduct->product_image;
                }
        
                return $order;
            });
        
            return response()->json([
                'success' => true,
                'message' => 'Orders fetched successfully.',
                'data' => $currentOrders
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated.'
            ], 401);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch orders.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
