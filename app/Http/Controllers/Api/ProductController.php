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

    public function packageItems()
    {
        // Only active Package products; eager-load the new flat packageItems fields
        $products = FlowerProduct::where('status', 'active')
            ->where('category', 'Package')
            ->with([
                'packageItems:id,product_id,item_name,quantity,unit,price',
            ])
            ->get();

        $data = $products->map(function ($p) {
            // normalize image URL if it’s a relative path
            $image = $p->product_image;
            $imageUrl = $image
                ? (Str::startsWith($image, ['http://', 'https://']) ? $image : url($image))
                : null;

            $base = [
                'product_id'    => $p->product_id,
                'name'          => $p->name,
                'odia_name'     => $p->odia_name,
                'product_image' => $imageUrl,
                'price'         => $p->price,
                'mrp'           => $p->mrp,
                'description'   => $p->description,
                'category'      => $p->category,
                'stock'         => $p->stock,
                'duration'      => $p->duration,
                'benefits'      => $p->benefits,
                'status'        => $p->status,
            ];

            // New format: item_name, quantity, unit, price
            $items = $p->packageItems->map(function ($pi) {
                return [
                    'item_name' => $pi->item_name ?? null,
                    'quantity'  => is_null($pi->quantity) ? null : (float) $pi->quantity,
                    'unit'      => $pi->unit ?? null,
                    'price'     => is_null($pi->price) ? null : (float) $pi->price,
                ];
            })->values();

            $base['package_items'] = $items;
            $base['package_total'] = (float) $p->packageItems->sum(function ($row) {
                return (float) ($row->price ?? 0);
            });

            return $base;
        })->values();

        return response()->json([
            'status'  => 200,
            'message' => 'Products retrieved successfully.',
            'data'    => $data,
        ], 200);
    }

}
