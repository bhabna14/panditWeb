<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\Order;

class ReportController extends Controller
{
    public function flowerPickupReport()
    {
        $vendors = FlowerVendor::where('status', 'active')->get();
        $riders = RiderDetails::where('status', 'active')->get();
    
        return view('admin.reports.flower-pickup-report', compact('vendors', 'riders'));
    }
    
    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);
    
        $query = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
            ->whereBetween('pickup_date', [$request->from_date, $request->to_date]);
    
        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }
    
        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }
    
        $reportData = $query->get();
    
        // Pass vendors and riders to keep dropdown populated
        $vendors = FlowerVendor::where('status', 'active')->get();
        $riders = RiderDetails::where('status', 'active')->get();
    
        return view('admin.reports.flower-pickup-report', compact('reportData', 'vendors', 'riders'));
    }


    public function showRevenueReport()
    {
        // Display an empty form on the initial page load
        $orders = [];
        $totalRevenue = 0;

        return view('admin.reports.revenue-report', compact('orders', 'totalRevenue'));
    }

    public function filterRevenueReport(Request $request)
    {
        // Validate the date inputs
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);
    
        // Fetch orders where the related subscription is active during the specified period
        $orders = Order::whereHas('subscription', function ($query) use ($request) {
            $query->where('status', 'active')
                  ->whereBetween('created_at', [$request->from_date, $request->to_date]); // Filter subscriptions by start_date
        })
        ->with(['user', 'flowerPayments', 'subscription']) // Eager load subscription
        ->get();
    
        // Calculate total revenue
        $totalRevenue = $orders->sum(function ($order) {
            return $order->flowerPayments->sum('paid_amount'); // Assuming `paid_amount` is the payment column
        });
    
        return view('admin.reports.revenue-report', compact('orders', 'totalRevenue'));
    }
    
    
}