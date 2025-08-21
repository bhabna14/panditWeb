<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use Illuminate\Support\Str;
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

    public function getFlowerProducts()
    {
        try {
            // Fetch only category 'flower' and required fields
            $products = FlowerProduct::where('category', 'Flower')->where('status', 'active')
                ->select('name', 'odia_name', 'mala_provided', 'is_flower_available', 'description')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ProductOrdersList()
    {
        try {
            $userId = Auth::guard('sanctum')->user()->userid;

            // Direct orders (no request_id)
            $subscriptionsOrder = ProductOrder::whereNull('request_id')
                ->where('user_id', $userId)
                ->with([
                    'subscription',
                    'flowerPayments',
                    'user',
                    'address.localityDetails',
                    // ⬇️ load packageItems under flowerProduct
                    'flowerProduct' => function ($q) {
                        $q->with('packageItems');
                    },
                ])
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($order) {
                    if ($order->flowerProduct && $order->flowerProduct->product_image) {
                        $order->flowerProduct->product_image_url = url($order->flowerProduct->product_image);
                    }

                    // Only keep packageItems visible for package category
                    if ($order->flowerProduct) {
                        $category = strtolower($order->flowerProduct->category ?? '');
                        if ($category !== 'package') {
                            // hide relation for non-package products (keeps payload clean)
                            $order->flowerProduct->setRelation('packageItems', collect());
                        }
                    }

                    return $order;
                });

            // Requested orders
            $requestedOrders = ProductRequest::where('user_id', $userId)
                ->with([
                    'order' => function ($query) {
                        $query->with('flowerPayments');
                    },
                    // ⬇️ load packageItems under flowerProduct
                    'flowerProduct' => function ($q) {
                        $q->with('packageItems');
                    },
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

                    // Only keep packageItems visible for package category
                    if ($request->flowerProduct) {
                        $category = strtolower($request->flowerProduct->category ?? '');
                        if ($category !== 'package') {
                            $request->flowerProduct->setRelation('packageItems', collect());
                        }
                    }

                    return $request;
                });

            return response()->json([
                'success' => 200,
                'data' => [
                    'subscriptions_order' => $subscriptionsOrder,
                    'requested_orders'    => $requestedOrders,
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
