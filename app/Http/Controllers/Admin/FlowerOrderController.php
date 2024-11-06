<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
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
                       
        return view('admin.flower-order.manage-flower-order', compact('orders'));
    }
    
    public function show($id)
{
    $order = Order::with(['flowerRequest', 'subscription', 'flowerPayments', 'user','flowerProduct','address'])->findOrFail($id);
    // $relatedOrders = Order::where('user_id', $order->user_id)
    //                       ->whereNotNull('request_id')
    //                       ->get();

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

}
