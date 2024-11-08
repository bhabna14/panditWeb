<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FlowerOrderController extends Controller
{
    //
    public function showOrders()
    {
        $orders = Order::whereNull('request_id')
                       ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user','flowerProduct','address'])
                       ->orderBy('id', 'desc')
                       ->get();
                       $activeSubscriptions = Subscription::where('status', 'active')->count();

                       // Paused subscriptions count
                       $pausedSubscriptions = Subscription::where('status', 'paused')->count();
               
                       // Orders requested today
                       $ordersRequestedToday = Subscription::whereDate('created_at', Carbon::today())->count();
                       
        return view('admin.flower-order.manage-flower-order', compact('orders','activeSubscriptions', 'pausedSubscriptions', 'ordersRequestedToday'));
    }
    
    public function show($id)
    {
        $order = Order::with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])->findOrFail($id);

    
    
        return view('admin.flower-request.show-order-details', compact('order'));
    }
    

public function showRequestOrders()
{
    // Retrieve all FlowerRequest records with their associated orders and flower payments
    $requestedOrders = FlowerRequest::with([
        'order' => function ($query) {
            $query->with('flowerPayments');
        },
        'flowerProduct',
        'user',
        'address'
    ])
    ->orderBy('id', 'asc')
    ->get();

    // Pass all requested orders to the view
    return view('admin.flower-order.manage-request-orders', compact('requestedOrders'));
}
public function showActiveSubscriptions()
{
    $activeSubscriptions = Subscription::where('status', 'active')
        ->with(['relatedOrder.flowerRequest', 'relatedOrder.flowerPayments', 'relatedOrder.user', 'relatedOrder.flowerProduct', 'relatedOrder.address'])
        ->get();

    return view('admin.flower-order.manage-active-subscriptions', compact('activeSubscriptions'));
}
public function showPausedSubscriptions()
{
    $pausedSubscriptions = Subscription::where('status', 'paused')
        ->with(['relatedOrder.flowerRequest', 'relatedOrder.flowerPayments', 'relatedOrder.user', 'relatedOrder.flowerProduct', 'relatedOrder.address'])
        ->get();

    return view('admin.flower-order.manage-paused-subscriptions', compact('pausedSubscriptions'));
}

public function showOrdersToday()
{
    $today = \Carbon\Carbon::today();
    $ordersRequestedToday = Subscription::whereDate('start_date', $today)
        ->with(['order.flowerPayments', 'order.user', 'order.flowerProduct', 'order.address'])
        ->get();

    return view('admin.flower-order.manage-today-requestorder', compact('ordersRequestedToday'));
}

}
