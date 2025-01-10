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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SubscriptionPauseResumeLog;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FlowerOrderController extends Controller
{
    //
    public function showOrders(Request $request)
{
    $query = Order::whereNull('request_id')
                  ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
                  ->orderBy('id', 'desc'); // This orders by the Order ID

    // Filter for non-assigned riders
    if ($request->query('filter') === 'rider') {
        $query->whereHas('subscription.relatedOrder', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('rider_id')
                  ->orWhere('rider_id', '');
            });
        });
    }

    // Check if the filter is for renewed subscriptions
    if ($request->query('filter') === 'renewed') {
        $query->whereDate('created_at', Carbon::today())
              ->whereIn('user_id', function ($subQuery) {
                  $subQuery->select('user_id')
                           ->from('orders')
                           ->whereDate('created_at', '<', Carbon::today());
              });
    }

    if ($request->query('filter') === 'end') {
        $query->whereHas('subscription', function ($subQuery) {
            $subQuery->whereDate('end_date', Carbon::today())
                     ->where('status', 'active');
        });
    }

    // Filter for new user subscriptions
    if ($request->query('filter') === 'new') {
        $query->whereDate('created_at', Carbon::today())
              ->whereNotIn('user_id', function ($subQuery) {
                  $subQuery->select('user_id')
                           ->from('orders')
                           ->whereDate('created_at', '<', Carbon::today())
                           ->whereNull('request_id'); // Ensure request_id is NULL in the subquery
              });
    }

    // Filter for active subscriptions
    if ($request->query('filter') === 'active') {
        $query->whereHas('subscription', function ($subQuery) {
            $subQuery->where('status', 'active');
        });
    }
    
    // Filter for expired subscriptions without a new subscription
    if ($request->query('filter') === 'expired') {
        $query->whereDoesntHave('subscription', function ($subQuery) {
            $subQuery->whereIn('status', ['active', 'paused', 'resume']);
        })->where('status', 'expired');
    }
    

    // Filter for paused subscriptions
    if ($request->query('filter') === 'paused') {
        $query->whereHas('subscription', function ($subQuery) {
            $subQuery->where('status', 'paused');
        });
    }

    // Retrieve the filtered orders
    $orders = $query->get();
    
    $activeSubscriptions = Subscription::where('status', 'active')->count();
    $pausedSubscriptions = Subscription::where('status', 'paused')->count();
    $ordersRequestedToday = Subscription::whereDate('created_at', Carbon::today())->count();
    $riders = RiderDetails::where('status', 'active')->get();
    
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
    
public function showorderdetails($id)
    {
        $order = Order::with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])->findOrFail($id);

    
    
        return view('admin.flower-order.show-order-details', compact('order'));
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
    // Fetch the orders with unique user_id where request_id is null and subscription status is expired
    $expiredSubscriptions = Order::whereNull('request_id')
        ->whereHas('subscription', function ($query) {
            $query->where('status', 'expired');
        })
        ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
        ->distinct('user_id') // Use distinct to fetch unique user_id
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

public function mngdeliveryhistory(Request $request)
{
    try {
        $filter = $request->input('filter', 'all');

        // Build the query
        $query = DeliveryHistory::with([
            'order.user',
            'order.flowerProduct',
            'order.flowerPayments',
            'order.address.localityDetails',
            'rider'
        ])->orderBy('created_at', 'desc');

        // Apply date filters
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->input('from_date')),
                Carbon::parse($request->input('to_date'))
            ]);
        }

        // Apply rider filter
        if ($request->filled('rider_id')) {
            $query->where('rider_id', $request->input('rider_id'));
        }

        // Apply predefined filter (e.g., today or monthly)
        if ($filter == 'todaydelivery') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter == 'monthlydelivery') {
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        }

        // Fetch results
        $deliveryHistory = $query->get();

        // Total deliveries for today
        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', Carbon::today())->count();

        // Get active riders for dropdown
        $riders = RiderDetails::where('status', 'active')->get();

        return view('admin.flower-order.manage-delivery-history', compact('deliveryHistory', 'totalDeliveriesToday', 'riders'));
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

public function updateAddress(Request $request, $id)
{
    // Find the address by ID
    $address = UserAddress::findOrFail($id);

    // Update the address fields
    $address->apartment_flat_plot = $request->input('apartment_flat_plot');
    $address->apartment_name = $request->input('apartment_name');
    $address->locality = $request->input('locality_name');
    $address->landmark = $request->input('landmark');
    $address->pincode = $request->input('pincode');
    $address->city = $request->input('city');
    $address->state = $request->input('state');

    // Save the updated address
    $address->save();

    // Redirect back with success message
    return redirect()->back()->with('success', 'Address updated successfully.');
}


public function updatePrice(Request $request, $id)
{
    // Validate the incoming request
    $request->validate([
        'total_price' => 'required|numeric|min:0',
    ]);

    // Find the order by ID
    $order = Order::findOrFail($id);

    // Update the total price
    $order->total_price = $request->input('total_price');
    $order->save();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Order price updated successfully!');
}

public function updatePaymentStatus(Request $request, $order_id)
{
    $request->validate([
        'payment_status' => 'required|string|in:pending,paid',
    ]);

    $payment = FlowerPayment::where('order_id', $order_id)->first();

    if (!$payment) {
        return redirect()->back()->with('error', 'Payment record not found.');
    }

    $payment->update([
        'payment_status' => $request->payment_status,
    ]);

    return redirect()->back()->with('success', 'Payment status updated successfully.');
}


public function pause(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

        // Validate input dates
        $pauseStartDate = Carbon::parse($request->pause_start_date);
        $pauseEndDate = Carbon::parse($request->pause_end_date);
        $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates

        // Get the most recent new_end_date or default to the original end_date
        $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
            ->orderBy('id', 'desc')
            ->value('new_end_date');

        // Use the most recent new_end_date for recalculating the new end date
        $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);

        // Calculate the new end date by adding paused days
        $newEndDate = $currentEndDate->addDays($pausedDays);

        // Update the subscription status and new date field
        $subscription->update([
            'status' => 'paused',
            'pause_start_date' => $pauseStartDate,
            'pause_end_date' => $pauseEndDate,
            'new_date' => $newEndDate,
        ]);

        // Log the pause action
        SubscriptionPauseResumeLog::create([
            'subscription_id' => $subscription->subscription_id,
            'order_id' => $order_id,
            'action' => 'paused',
            'pause_start_date' => $pauseStartDate,
            'pause_end_date' => $pauseEndDate,
            'paused_days' => $pausedDays,
            'new_end_date' => $newEndDate,
        ]);

        return redirect()->back()->with('success', 'Successfully paused subscription.');

    } catch (\Exception $e) {
        // Log any errors that occur during the process
        Log::error('Error pausing subscription', [
            'order_id' => $order_id,
            'error_message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to pause subscription.');
    }
}


public function resume(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

        // Validate that the subscription is currently paused
        if ($subscription->status !== 'paused') {
            return redirect()->back()->with('error', 'Subscription is not in a paused state.');
        }

        // Parse the dates
        $resumeDate = Carbon::parse($request->resume_date);
        $pauseStartDate = Carbon::parse($subscription->pause_start_date);
        $pauseEndDate = Carbon::parse($subscription->pause_end_date);
        $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);

        // Ensure the resume date is within the pause period
        if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
            return redirect()->back()->with('error', 'Resume date must be within the pause period.');
        }

        // Calculate the days actually paused until the resume date
        $actualPausedDays = $resumeDate->diffInDays($pauseStartDate) + 1;

        // Adjust the new end date by subtracting the paused days
        $newEndDate = $currentEndDate->subDays($actualPausedDays);

        // Update the subscription
        $subscription->update([
            'status' => 'active',
            'pause_start_date' => null,
            'pause_end_date' => null,
            'new_date' => $newEndDate,
        ]);

        // Log the resume action
        SubscriptionPauseResumeLog::create([
            'subscription_id' => $subscription->subscription_id,
            'order_id' => $order_id,
            'action' => 'resumed',
            'resume_date' => $resumeDate,
            'pause_start_date' => $pauseStartDate,
            'new_end_date' => $newEndDate,
            'paused_days' => $actualPausedDays,
        ]);

        return redirect()->back()->with('success', 'Successfully resumed subscription.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to resume subscription.');
    }
}


}
