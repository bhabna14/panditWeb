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

}
