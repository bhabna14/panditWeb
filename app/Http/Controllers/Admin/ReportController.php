<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
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
    
}