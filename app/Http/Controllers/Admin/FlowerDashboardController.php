<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Subscription;
use App\Models\FlowerPickupDetails;
use App\Models\RiderDetails;
use App\Models\FlowerRequest;
use App\Models\SubscriptionPauseResumeLog;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Carbon\Carbon;

class FlowerDashboardController extends Controller
{
    public function flowerDashboard()
{
    $activeSubscriptions = Subscription::where('status', 'active')->count();

     $totalDeliveriesTodayCount = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
        ->count();


    $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())
        ->where('delivery_status', 'delivered')
        ->get();

    $totalIncomeToday = 0;

    foreach ($totalDeliveriesToday as $delivery) {
        $order = $delivery->order;
        if (!$order) continue;

        $subscription = Subscription::where('order_id', $order->order_id)->first();
        if (!$subscription || !$subscription->start_date || !$subscription->end_date) continue;

        $start = Carbon::parse($subscription->start_date);
        $end = Carbon::parse($subscription->end_date);
        $days = $start->diffInDays($end) + 1;

        if ($days > 0 && $order->total_price > 0) {
            $totalIncomeToday += $order->total_price / $days;
        }
    }

      $todayTotalExpenditure = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())
        ->sum('total_price');

          $riders = RiderDetails::where('status','active')->get();

          $ridersData = $riders->map(function ($rider) {
            // Total assigned orders to this rider
            $totalAssignedOrders = Order::where('rider_id', $rider->rider_id) // Filter by rider_id
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active'); // Check subscription status
            })
            ->count();

            // Total delivered orders today by this rider
            $totalDeliveredToday = DeliveryHistory::whereDate('created_at', Carbon::today())
                ->where('rider_id', $rider->rider_id)
                ->where('delivery_status', 'delivered')
                ->count();

            return [
                'rider' => $rider,
                'totalAssignedOrders' => $totalAssignedOrders,
                'totalDeliveredToday' => $totalDeliveredToday,
            ];
        });

        $totalRiders = RiderDetails::where('status','active')->count();

        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
            ->count();

        $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('delivery_status', 'delivered')
            ->count();

        $totalDeliveries = DeliveryHistory::where('delivery_status', 'delivered')->count();

         $newUserSubscription = Subscription::whereDate('created_at', Carbon::today())
        ->distinct('user_id')
        ->where('status', 'pending')                   
        ->count('user_id');

        $renewSubscription = Subscription::whereDate('created_at', Carbon::today()) // Check rows created today
        ->whereIn('order_id', function ($query) {
            $query->select('order_id')
                ->from('subscriptions')
                ->groupBy('order_id')
                ->havingRaw('COUNT(order_id) > 1'); // Find duplicate order IDs
        })
        ->count();

         $todayEndSubscription = Subscription::where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('new_date') // Check if new_date is available
                            ->whereDate('new_date', Carbon::today()); // Count using new_date if available
                })
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNull('new_date') // Check if new_date is not available
                            ->whereDate('end_date', Carbon::today()); // Count using end_date
                });
            })
            ->where('status', 'active') // Status must be active
            ->count();

            $subscriptionEndFiveDays = Subscription::where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('new_date')
                        ->whereBetween('new_date', [
                            Carbon::today()->subDays(4), // 4 days ago
                            Carbon::today()              // today
                        ]);
                })->orWhere(function ($subQuery) {
                    $subQuery->whereNull('new_date')
                        ->whereBetween('end_date', [
                            Carbon::today()->subDays(4),
                            Carbon::today()
                        ]);
                });
            })
            ->where('status', 'active')
            ->count();

            $expiredSubscriptions = Subscription::where('status', 'expired')
                ->whereNotIn('user_id', function ($query) {
                    $query->select('user_id')
                        ->from('subscriptions')
                        ->whereIn('status', ['active', 'paused', 'resume']);
                })
                ->distinct('user_id')
                ->latest('end_date')
                ->count();

            $nonAssignedRidersCount = Subscription::with('relatedOrder')
            ->where('status', 'active')
            ->whereHas('relatedOrder', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('rider_id')
                        ->orWhere('rider_id', '');
                });
            })
            ->count();
        
            $ordersRequestedToday = FlowerRequest::whereDate('created_at', Carbon::today())->count();

            $pausedSubscriptions = Subscription::where('status', 'paused')->count();

            $tomorrow = Carbon::tomorrow()->toDateString();

            $nextDayPaused = Subscription::where('status', 'active')
            ->whereDate('pause_start_date', $tomorrow)
            ->count();

            $nextDayResumed = Subscription::where('status', 'active')
            ->whereDate('pause_end_date', $tomorrow)
            ->count();

            $todayPausedRequest = SubscriptionPauseResumeLog::whereDate('created_at', Carbon::today())
            ->where('action', 'paused') // Optional: only count pause actions
            ->count();

            $today = Carbon::today();
            $threeDaysLater = Carbon::today()->addDays(3);

            $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$today, $threeDaysLater])
            ->count();

            return view('admin/flower-dashboard', compact(
                'activeSubscriptions',
                'totalDeliveriesTodayCount',
                'totalIncomeToday',
                'todayTotalExpenditure',
                'ridersData',
                'totalRiders',
                'totalDeliveriesToday',
                'totalDeliveriesThisMonth',
                'totalDeliveries',
                'newUserSubscription',
                'renewSubscription',
                'ordersRequestedToday',
                'todayEndSubscription',
                'subscriptionEndFiveDays',
                'expiredSubscriptions',
                'nonAssignedRidersCount',
                'todayPausedRequest',
                'pausedSubscriptions',
                'nextDayPaused',
                'nextDayResumed',
                'upcomingCustomizeOrders'
            ));
}

  public function showTodayDeliveries()
{
    $today = Carbon::today();

    $deliveries = DeliveryHistory::with(['order.user', 'rider'])
        ->whereDate('created_at', $today)
        ->where('delivery_status', 'delivered')
        ->get();

    $totalIncome = 0;

    foreach ($deliveries as $delivery) {
        $order = $delivery->order;
        if (!$order) continue;

        // Get subscription by order_id
        $subscription = Subscription::where('order_id', $order->order_id)->first();
        if (!$subscription || !$subscription->start_date || !$subscription->end_date) continue;

        $start = Carbon::parse($subscription->start_date);
        $end = Carbon::parse($subscription->end_date);
        $days = $start->diffInDays($end) + 1;

        if ($days > 0 && $order->total_price > 0) {
            $totalIncome += $order->total_price / $days;
        }
    }

    return view('admin.today-delivery-data', compact('deliveries', 'totalIncome'));
}

}
