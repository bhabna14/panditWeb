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
                ->select('name', 'odia_name', 'mala_provided', 'flower_available', 'description')
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


public function packageItems()
{
    // Eager-load package items + their item & variant to avoid N+1 queries
    $products = FlowerProduct::where('status', 'active')->where('category', 'Package')
        ->with([
            'packageItems:id,product_id,item_id,variant_id',
            'packageItems.item:id,item_name,product_type,status',
            'packageItems.variant:id,item_id,title,price',
        ])
        ->get();

    // Transform response: attach package_items only for Package category
    $data = $products->map(function ($p) {
        $base = [
            'product_id'         => $p->product_id,
            'name'               => $p->name,
            'odia_name'          => $p->odia_name,
            'product_image'      => $p->product_image,
            'price'              => $p->price,
            'mrp'                => $p->mrp,
            'description'        => $p->description,
            'category'           => $p->category,
            'stock'              => $p->stock,
            'duration'           => $p->duration,
            'benefits'           => $p->benefits,
            'status'             => $p->status,
        ];

        if (Str::lower($p->category) === 'package') {
            $base['package_items'] = $p->packageItems
                ->map(function ($pi) {
                    return [
                        'item_id'        => $pi->item_id,
                        'item_name'      => optional($pi->item)->item_name,
                        'variant_id'     => $pi->variant_id,
                        'variant_title'  => optional($pi->variant)->title,
                        'variant_price'  => optional($pi->variant)->price,
                    ];
                })
                ->values();
        }

        return $base;
    });

    return response()->json([
        'status'  => 200,
        'message' => 'Products retrieved successfully.',
        'data'    => $data,
    ], 200);
}
}
