<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductOrder;
use App\Models\ProductSucription;


use App\Models\RiderDetails;
use Carbon\Carbon;

class ProductSubscriptionController extends Controller
{
    public function showProductOrder(Request $request)
    {
        $query = ProductOrder::whereNull('request_id')
                      ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                      ->orderBy('created_at', 'desc');
    
        // Check if the filter is for renewed subscriptions
        if ($request->query('filter') === 'renewed') {
            $query->whereDate('created_at', Carbon::today())
                ->whereIn('user_id', function ($subQuery) {
                    $subQuery->select('user_id')
                            ->from('product__orders_details')
                            ->whereDate('created_at', '<', Carbon::today());
                });
        }

        // Filter for new user subscriptions
        if ($request->query('filter') === 'new') {
            $query->whereDate('created_at', Carbon::today())
                ->whereNotIn('user_id', function ($subQuery) {
                    $subQuery->select('user_id')
                            ->from('product__orders_details')
                            ->whereDate('created_at', '<', Carbon::today())
                            ->whereNull('request_id'); // Ensure request_id is NULL in the subquery
                });
        }

        // Filter for active subscriptions
        if ($request->query('filter') === 'active') {
            $query->whereHas('product__subscriptions_details', function ($subQuery) {
                $subQuery->where('status', 'active');
            });
        }
    
         // Filter for expired subscriptions without a new subscription
        if ($request->query('filter') === 'expired') {
            $query->whereHas('product__subscriptions_details', function ($subQuery) {
                $subQuery->where('status', 'expired')
                        ->whereNotIn('user_id', function ($nestedQuery) {
                            $nestedQuery->select('user_id')
                                        ->from('product__subscriptions_details')
                                        ->where('status', 'active');
                        });
            });
        }


        $orders = $query->get();
    
        $activeSubscriptions = ProductSucription::where('status', 'active')->count();
        $ordersRequestedToday = ProductSucription::whereDate('created_at', Carbon::today())->count();
        $riders = RiderDetails::where('status', 'active')->get();
        
        return view('admin.product-package.manage-product-order', compact(
            'riders', 'orders', 'activeSubscriptions', 'ordersRequestedToday'
        ));
    }
    
}
