<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Subscription;
use Illuminate\Http\Request;

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

        $totalIncome = $deliveries->sum(function ($delivery) {
            return $delivery->order->total_price ?? 0; // Make sure `total_price` exists in the Order model
        });

        return view('admin.today-deliveries', compact('deliveries', 'totalIncome'));
    }

}
