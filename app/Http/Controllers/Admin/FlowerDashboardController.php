<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FlowerDashboardController extends Controller
{
    public function flowerDashboard()
    {
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
        ->count();


            return view('admin/flower-dashboard', compact(
                'totalDeliveriesToday',
                'activeSubscriptions',
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
