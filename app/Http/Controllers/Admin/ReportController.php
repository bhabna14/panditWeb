<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;

class ReportController extends Controller
{
    public function flowerPickupReport()
    {
        return view('admin.reports.flower-pickup-report');
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $reportData = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
            ->whereBetween('pickup_date', [$request->from_date, $request->to_date])
            ->get();

        return view('admin.reports.flower-pickup-report', compact('reportData'));
    }
}