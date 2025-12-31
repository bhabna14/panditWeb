<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RiderAttendance;
use Illuminate\Validation\Rule;


class RiderAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 1) Read filters safely
        $monthInput = trim((string) $request->query('month', now()->format('Y-m')));
        if (!preg_match('/^\d{4}\-\d{2}$/', $monthInput)) {
            $monthInput = now()->format('Y-m');
        }

        $selectedRiderId = $request->query('rider_id');
        $selectedRiderId = $selectedRiderId !== null ? trim((string) $selectedRiderId) : null;

        // 2) Month meta
        $year      = (int) substr($monthInput, 0, 4);
        $monthNo   = (int) substr($monthInput, 5, 2);
        $startOfMonth = Carbon::create($year, $monthNo, 1)->startOfMonth()->startOfDay();
        $endOfMonth   = Carbon::create($year, $monthNo, 1)->endOfMonth()->endOfDay();
        $daysInMonth  = $startOfMonth->daysInMonth;

        // 3) Riders list
        $riders = RiderDetails::query()
            ->orderBy('rider_name')
            ->get();

        if ((!$selectedRiderId || $selectedRiderId === '') && $riders->count() > 0) {
            $selectedRiderId = (string) $riders->first()->rider_id;
        }

        // 4) Use created_at if it exists, otherwise delivery_time
        //    This fixes "filter not working" when created_at is null in rows.
        $dateExpr = "COALESCE(created_at, delivery_time)";

        // Base query factory (so we don’t repeat logic and we can reuse reliably)
        $baseHistoryQuery = function () use ($dateExpr, $year, $monthNo) {
            return DB::table('delivery_history')
                ->whereRaw("$dateExpr IS NOT NULL")
                ->whereRaw("YEAR($dateExpr) = ?", [$year])
                ->whereRaw("MONTH($dateExpr) = ?", [$monthNo]);
        };

        // 5) Selected rider daily counts
        $dailyCounts = [];
        $selectedRider = null;

        if ($selectedRiderId) {
            $selectedRider = $riders->firstWhere('rider_id', $selectedRiderId);

            $dailyCounts = $baseHistoryQuery()
                ->where('rider_id', $selectedRiderId)
                ->selectRaw("DATE($dateExpr) as dt, COUNT(*) as deliveries")
                ->groupBy('dt')
                ->pluck('deliveries', 'dt')
                ->toArray();
        }

        // 6) All riders summary for the month
        $summaryRows = $baseHistoryQuery()
            ->selectRaw("rider_id, COUNT(DISTINCT DATE($dateExpr)) as present_days, COUNT(*) as deliveries")
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

        // 7) Totals for selected rider
        $presentDays = 0;
        $totalDeliveries = 0;

        foreach ($dailyCounts as $dt => $cnt) {
            $cnt = (int) $cnt;
            if ($cnt > 0) {
                $presentDays++;
                $totalDeliveries += $cnt;
            }
        }

        $absentDays = max(0, $daysInMonth - $presentDays);

        return view('admin.rider-attendance.index', [
            'month'            => $monthInput,
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

     public function indexAttendance(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        if (!preg_match('/^\d{4}\-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $selectedRiderId = $request->query('rider_id');

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->startOfDay();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->endOfDay();

        $riders = RiderDetails::query()->orderBy('rider_name')->get();

        $q = RiderAttendance::query()
            ->with('rider')
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('id', 'desc');

        if ($selectedRiderId) {
            $q->where('rider_id', $selectedRiderId);
        }

        $attendances = $q->paginate(31)->withQueryString();

        // Summary counts for the month (respect rider filter)
        $summaryQuery = RiderAttendance::query()
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);
        if ($selectedRiderId) {
            $summaryQuery->where('rider_id', $selectedRiderId);
        }

        $summary = [
            'present'  => (clone $summaryQuery)->where('status', 'present')->count(),
            'absent'   => (clone $summaryQuery)->where('status', 'absent')->count(),
            'leave'    => (clone $summaryQuery)->where('status', 'leave')->count(),
            'half_day' => (clone $summaryQuery)->where('status', 'half_day')->count(),
        ];

        return view('admin.rider-attendance.manual', compact(
            'month',
            'selectedRiderId',
            'start',
            'end',
            'riders',
            'attendances',
            'summary'
        ));
    }

    /**
     * Save manual attendance (insert or update for same rider+date)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rider_id' => [
                'required',
                Rule::exists('flower__rider_details', 'rider_id'),
            ],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['present', 'absent', 'leave', 'half_day'])],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Compute working minutes if both times present
        $workingMinutes = null;
        if (!empty($validated['check_in_time']) && !empty($validated['check_out_time'])) {
            $in  = Carbon::createFromFormat('H:i', $validated['check_in_time']);
            $out = Carbon::createFromFormat('H:i', $validated['check_out_time']);

            // If out < in, assume next day (night shift)
            if ($out->lt($in)) {
                $out->addDay();
            }

            $workingMinutes = $in->diffInMinutes($out);
        }

        RiderAttendance::updateOrCreate(
            [
                'rider_id' => $validated['rider_id'],
                'attendance_date' => Carbon::parse($validated['attendance_date'])->toDateString(),
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'working_minutes' => $workingMinutes,
                'remarks' => $validated['remarks'] ?? null,
                'marked_by' => auth()->id(), // optional; remove if not using auth
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Attendance saved successfully.');
    }
}
