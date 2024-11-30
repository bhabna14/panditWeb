<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Notification;
use App\Models\RiderDetails;
use App\Models\DeliveryHistory;

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
                       ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user','flowerProduct','address.localityDetails'])
                       ->orderBy('id', 'desc')
                       ->get();
                       $activeSubscriptions = Subscription::where('status', 'active')->count();

                       // Paused subscriptions count
                       $pausedSubscriptions = Subscription::where('status', 'paused')->count();
               
                       // Orders requested today
                       $ordersRequestedToday = Subscription::whereDate('created_at', Carbon::today())->count();
        $riders = RiderDetails::where('status', 'active')
                      
                       ->get();           
        return view('admin.flower-order.manage-flower-order', compact('riders','orders','activeSubscriptions', 'pausedSubscriptions', 'ordersRequestedToday'));
    }

    // In your Controller:
public function showNotifications()
{
    $notifications = Notification::where('is_read', false)->latest()->get();
    return view('admin.layouts.components.app-header', compact('notifications'));
}

    public function showCustomerDetails($userid)
    {
        // Fetch user details by `userid` instead of `id`
        $user = User::where('userid', $userid)->firstOrFail();
        $addressdata = UserAddress::where('user_id', $userid)
                                ->where('status','active')
                                ->get();
    
        // Fetch user orders based on `userid`
        // $orders = Order::where('user_id', $userid)
        //                ->with(['flowerProduct', 'subscription', 'flowerPayments', 'address'])
        //                ->orderBy('id', 'desc')
        //                ->get();

        $orders = Order::where('user_id', $userid)
    ->whereHas('subscription', function ($query) {
        // This ensures that only orders with a related subscription are included
        $query->whereColumn('orders.order_id', 'subscriptions.order_id');
    })
    ->with(['flowerProduct', 'subscription', 'flowerPayments', 'address.localityDetails'])
    ->orderBy('id', 'desc')
    ->get();


    $pendingRequests = FlowerRequest::where('user_id', $userid)
        ->with([
            'flowerProduct',
            'user',
            'address',
            'flowerRequestItems' // Eager load flowerRequestItems
        ])
        ->orderBy('id', 'desc')
        ->get();
    
    // Step 2: For each flower request, check if an associated order exists
    foreach ($pendingRequests as $request) {
        $request->order = Order::where('request_id', $request->request_id)
            ->with('flowerPayments')
            ->first();
    }
    
    // Now $flowerRequests will have the associated order data if it exists
    
    

        $totalOrders = Order::where('user_id', $userid)->count();
        $ongoingOrders = Order::where('user_id', $userid)
                          ->whereHas('subscription', function ($query) {
                              $query->where('status', 'active'); // Adjust status value as needed
                          })
                          ->count();

    // Total spend
    $totalSpend = Order::where('user_id', $userid)->sum('total_price'); // Adjust column name if necessary

  
        // Return the view with user and orders data
        return view('admin.flower-order.show-customer-details', compact('user','addressdata','pendingRequests', 'orders','totalOrders', 'ongoingOrders', 'totalSpend'));
    }
    

    
    public function show($id)
    {
        $order = Order::with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])->findOrFail($id);

    
    
        return view('admin.flower-request.show-order-details', compact('order'));
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

public function assignRider(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);
    $order->rider_id = $request->rider_id;
    $order->save();

    return redirect()->back()->with('success', 'Rider assigned successfully.');
}
public function updateRider(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);
    $order->rider_id = $request->rider_id;  // Update rider_id field
    $order->save();  // Save the changes

    return redirect()->back()->with('success', 'Rider updated successfully.');
}

public function mngdeliveryhistory()
{
    try {
        $deliveryHistory = DeliveryHistory::with([
            'order.user',                   // Fetch user details
            'order.flowerProduct',          // Fetch product details
            'order.flowerPayments',         // Fetch payment details
            'order.address.localityDetails', // Fetch address details
            'rider'                         // Fetch rider details
        ])->orderBy('created_at', 'desc')->get();

        return view('admin.flower-order.manage-delivery-history', compact('deliveryHistory'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Failed to fetch delivery history: ' . $e->getMessage()]);
    }
}


}
