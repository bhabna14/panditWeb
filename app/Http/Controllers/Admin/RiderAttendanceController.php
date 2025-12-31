<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RiderAttendanceController extends Controller
{
     public function index(Request $request)
    {
        // Month filter: YYYY-MM (default current month)
        $month = $request->get('month', now()->format('Y-m'));

        // Rider filter (optional)
        $selectedRiderId = $request->get('rider_id');

        // Parse month range
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->startOfDay();
        $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->endOfDay();

        // Riders list
        $riders = RiderDetails::query()
            ->orderBy('rider_name')
            ->get();

        // If rider not selected, pick first rider (if exists)
        if (!$selectedRiderId && $riders->count() > 0) {
            $selectedRiderId = $riders->first()->rider_id;
        }

        // -----------------------------
        // Selected Rider: attendance map (date => delivery count)
        // Present if count > 0
        // -----------------------------
        $dailyCounts = [];
        $selectedRider = null;

        if ($selectedRiderId) {
            $selectedRider = $riders->firstWhere('rider_id', $selectedRiderId);

            $dailyCounts = DB::table('delivery_history')
                ->selectRaw('DATE(created_at) as dt, COUNT(*) as deliveries')
                ->where('rider_id', $selectedRiderId)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->groupBy('dt')
                ->pluck('deliveries', 'dt')
                ->toArray();
        }

        // -----------------------------
        // All Riders Summary (for the month)
        // present_days = count(distinct date(created_at))
        // deliveries   = count(*)
        // -----------------------------
        $summaryRows = DB::table('delivery_history')
            ->selectRaw('rider_id, COUNT(DISTINCT DATE(created_at)) as present_days, COUNT(*) as deliveries')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('rider_id')
            ->get()
            ->keyBy('rider_id');

        $allRiderSummary = $riders->map(function ($r) use ($summaryRows) {
            $row = $summaryRows->get($r->rider_id);

            return [
                'rider_id'     => $r->rider_id,
                'rider_name'   => $r->rider_name,
                'phone_number' => $r->phone_number,
                'present_days' => (int) ($row->present_days ?? 0),
                'deliveries'   => (int) ($row->deliveries ?? 0),
            ];
        });

        // Days in month for calendar + totals
        $daysInMonth = $startOfMonth->daysInMonth;

        $presentDays = 0;
        $totalDeliveries = 0;

        foreach ($dailyCounts as $dt => $cnt) {
            if ((int)$cnt > 0) {
                $presentDays++;
                $totalDeliveries += (int)$cnt;
            }
        }

        $absentDays = $daysInMonth - $presentDays;

        return view('admin.rider-attendance.index', [
            'month'            => $month,
            'startOfMonth'     => $startOfMonth,
            'endOfMonth'       => $endOfMonth,
            'riders'           => $riders,
            'selectedRiderId'  => $selectedRiderId,
            'selectedRider'    => $selectedRider,
            'dailyCounts'      => $dailyCounts,
            'daysInMonth'      => $daysInMonth,
            'presentDays'      => $presentDays,
            'absentDays'       => $absentDays,
            'totalDeliveries'  => $totalDeliveries,
            'allRiderSummary'  => $allRiderSummary,
        ]);
    }
}
