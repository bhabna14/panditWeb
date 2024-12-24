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
use App\Models\FlowerPickupDetails;

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
                   ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                   ->orderBy('created_at', 'desc')
                   ->get();

    $activeSubscriptions = Subscription::where('status', 'active')->count();
    $pausedSubscriptions = Subscription::where('status', 'paused')->count();
    $ordersRequestedToday = Subscription::whereDate('created_at', Carbon::today())->count();
    $riders = RiderDetails::where('status', 'active')->get();
    
    // Count unviewed orders
    // $unviewedOrdersCount = Order::where('is_viewed', false)->count();

    return view('admin.flower-order.manage-flower-order', compact(
        'riders', 'orders', 'activeSubscriptions', 'pausedSubscriptions', 'ordersRequestedToday'
    ));
}
public function markAsViewed()
{
    Order::where('is_viewed', false)->update(['is_viewed' => true]);
    return response()->json(['message' => 'Orders marked as viewed.'], 200);
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
    $activeSubscriptions = Order::whereNull('request_id')
    ->whereHas('subscription', function ($query) {
        $query->where('status', 'active');
    })
    ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
    ->orderBy('created_at', 'desc')
    ->get();

    return view('admin.flower-order.manage-active-subscriptions', compact('activeSubscriptions'));
}
public function showPausedSubscriptions()
{
   
        $pausedSubscriptions = Order::whereNull('request_id')
        ->whereHas('subscription', function ($query) {
            $query->where('status', 'paused');
        })
        ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
        ->orderBy('created_at', 'desc')
        ->get();
    

    return view('admin.flower-order.manage-paused-subscriptions', compact('pausedSubscriptions'));
}
public function showexpiredSubscriptions()
{
    // Fetch expired subscriptions list whose new subscription is not created don not repeat the expired subscription with same user_id and i have all relationship with orders table
    $expiredSubscriptions = Order::whereNull('request_id')
    ->whereHas('subscription', function ($query) {
        $query->where('status', 'expired');
    })
    ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
    ->orderBy('created_at', 'desc')
    ->get();

  
    

    return view('admin.flower-order.manage-expired-subscriptions', compact('expiredSubscriptions'));
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
    $request->validate([
        'rider_id' => 'required|exists:flower__rider_details,rider_id',
    ]);
    $order = Order::findOrFail($orderId);
    $order->rider_id = $request->rider_id;
    $order->save();

    return redirect()->back()->with('success', 'Rider assigned successfully.');
}
public function refferRider(Request $request, $orderId)
{
    $request->validate([
        'referral_id' => 'required|exists:flower__rider_details,rider_id', // Ensure the referral_id exists in the riders table
    ]);

    $order = Order::findOrFail($orderId);
    $order->referral_id = $request->referral_id;
    $order->save();

    return redirect()->back()->with('success', 'Rider referred successfully.');
}


public function updateRider(Request $request, $orderId)
{
    // Validate the incoming data
    $validated = $request->validate([
        'rider_id' => 'required|exists:flower__rider_details,rider_id', // Ensure rider_id exists
    ]);

    // Find the order
    $order = Order::findOrFail($orderId);

    // Update the rider
    $order->rider_id = $request->rider_id;
    $order->save();

    // Redirect back with a success message
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
public function showRiderDetails($id)
{
    // Fetch rider details
    $rider = RiderDetails::findOrFail($id);

    // Fetch delivery history for the rider
    $deliveryHistory = DeliveryHistory::with([
        'order.user',
        'order.flowerProduct',
        'order.flowerPayments',
        'order.address.localityDetails',
        'rider'
    ])->where('rider_id', $rider->rider_id)
      ->orderBy('created_at', 'desc')
      ->get();
// add pickup history
    $pickupHistory = FlowerPickupDetails::with([
        'vendor',
        'rider',
        'flowerPickupItems',
    ])->where('rider_id', $rider->rider_id)
      ->orderBy('created_at', 'desc')
      ->get();

    // calculate tota_price
    $total_price = FlowerPickupDetails::where('rider_id', $rider->rider_id)->sum('total_price');
    //calculate total paid from pyament_status
    $total_paid = FlowerPickupDetails::where('rider_id', $rider->rider_id)->where('payment_status','Paid')->sum('total_price');
    //calculate total unpaid from pyament_status

    $total_unpaid = FlowerPickupDetails::where('rider_id', $rider->rider_id)->where('payment_status','pending')->sum('total_price');
    // Calculate total orders
    $totalOrders = $deliveryHistory->count();

    // Calculate ongoing orders
    $ongoingOrders = $deliveryHistory->where('delivery_status', 'ongoing')->count();

    // Calculate monthly orders
    $monthlyOrders = $deliveryHistory->whereBetween('created_at', [
        now()->startOfMonth(),
        now()->endOfMonth()
    ])->count();

    // Calculate total spend (optional)
    $totalSpend = $deliveryHistory->sum(function ($history) {
        return $history->order->flowerPayments->sum('paid_amount');
    });

    // Return to the Blade view
   
    return view('admin.rider-all-details', compact('total_price','total_paid','total_unpaid','rider','pickupHistory', 'deliveryHistory', 'totalOrders', 'ongoingOrders', 'monthlyOrders', 'totalSpend'));
}


}
