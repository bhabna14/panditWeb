<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiderAttendance;
use App\Models\RiderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RiderSalaryController extends Controller
{
    public function index(Request $request)
    {
        // Fallback salary if rider salary is null/empty
        $defaultMonthlySalary = 5000;

        // Attendance weights
        $weights = [
            'present'  => 1.0,
            'half_day' => 0.5,
            'leave'    => 0.0, // change to 1.0 if leave should be paid
            'absent'   => 0.0,
        ];

        // Month filter: YYYY-MM
        $month = trim((string) $request->query('month', now()->format('Y-m')));
        try {
            $monthObj = Carbon::createFromFormat('Y-m', $month);
        } catch (\Throwable $e) {
            $monthObj = now();
            $month = $monthObj->format('Y-m');
        }

        $selectedRiderId = $request->filled('rider_id') ? trim((string) $request->query('rider_id')) : null;

        $start = $monthObj->copy()->startOfMonth();
        $end   = $monthObj->copy()->endOfMonth();
        $fromDate = $start->toDateString();
        $toDate   = $end->toDateString();
        $daysInMonth = $start->daysInMonth;

        $riders = RiderDetails::query()->orderBy('rider_name')->get();

        // ===========================
        // Summary for ALL riders (attendance counts)
        // ===========================
        $rawSummary = RiderAttendance::query()
            ->select([
                'rider_id',
                DB::raw("SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present_days"),
                DB::raw("SUM(CASE WHEN status='half_day' THEN 1 ELSE 0 END) as half_days"),
                DB::raw("SUM(CASE WHEN status='leave' THEN 1 ELSE 0 END) as leave_days"),
                DB::raw("SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent_days"),
                DB::raw("COUNT(*) as marked_days"),
            ])
            ->whereDate('attendance_date', '>=', $fromDate)
            ->whereDate('attendance_date', '<=', $toDate)
            ->groupBy('rider_id')
            ->get()
            ->keyBy('rider_id');

        // ===========================
        // Salary summary per rider (uses rider_details.salary)
        // ===========================
        $allRiderSalary = $riders->map(function ($r) use ($rawSummary, $daysInMonth, $weights, $defaultMonthlySalary) {
            $row = $rawSummary->get($r->rider_id);

            $present = (int) ($row->present_days ?? 0);
            $half    = (int) ($row->half_days ?? 0);
            $leave   = (int) ($row->leave_days ?? 0);
            $absent  = (int) ($row->absent_days ?? 0);
            $marked  = (int) ($row->marked_days ?? 0);

            $notMarked = max(0, $daysInMonth - $marked);

            $gross = (float) ($r->salary ?? 0);
            if ($gross <= 0) {
                $gross = (float) $defaultMonthlySalary;
            }

            $perDay = $gross / $daysInMonth;

            $payableUnits =
                ($present * $weights['present']) +
                ($half * $weights['half_day']) +
                ($leave * $weights['leave']) +
                ($absent * $weights['absent']) +
                ($notMarked * 0.0);

            $payable   = round($perDay * $payableUnits, 2);
            $deduction = round(max(0, $gross - $payable), 2);

            return [
                'rider_id' => $r->rider_id,
                'rider_name' => $r->rider_name,
                'phone_number' => $r->phone_number,

                'gross' => round($gross, 2),
                'per_day' => round($perDay, 2),

                'present' => $present,
                'half_day' => $half,
                'leave' => $leave,
                'absent' => $absent,
                'not_marked' => $notMarked,

                'payable_units' => round($payableUnits, 2),
                'salary' => $payable,
                'deduction' => $deduction,
            ];
        });

        // ===========================
        // NEW: Payroll Summary Cards (Month Total)
        // ===========================
        $totalRiders    = $riders->count();
        $totalGross     = round($allRiderSalary->sum('gross'), 2);
        $totalPayable   = round($allRiderSalary->sum('salary'), 2);
        $totalDeduction = round($allRiderSalary->sum('deduction'), 2);

        $totalPresent   = (int) $allRiderSalary->sum('present');
        $totalHalfDay   = (int) $allRiderSalary->sum('half_day');
        $totalLeave     = (int) $allRiderSalary->sum('leave');
        $totalAbsent    = (int) $allRiderSalary->sum('absent');
        $totalNotMarked = (int) $allRiderSalary->sum('not_marked');

        $payPercent = ($totalGross > 0) ? round(($totalPayable / $totalGross) * 100, 2) : 0;

        $avgPayable = ($totalRiders > 0) ? round($totalPayable / $totalRiders, 2) : 0;
        $avgGross   = ($totalRiders > 0) ? round($totalGross / $totalRiders, 2) : 0;

        $payrollSummary = [
            'total_riders'     => $totalRiders,
            'total_gross'      => $totalGross,
            'total_payable'    => $totalPayable,
            'total_deduction'  => $totalDeduction,
            'pay_percent'      => $payPercent,
            'avg_payable'      => $avgPayable,
            'avg_gross'        => $avgGross,

            'present'          => $totalPresent,
            'half_day'         => $totalHalfDay,
            'leave'            => $totalLeave,
            'absent'           => $totalAbsent,
            'not_marked'       => $totalNotMarked,
        ];

        // ===========================
        // Selected rider: day-wise breakdown
        // ===========================
        $selectedRider = null;
        $dayRows = [];
        $riderTotals = null;

        if ($selectedRiderId) {
            $selectedRider = $riders->firstWhere('rider_id', $selectedRiderId);

            if ($selectedRider) {
                $gross = (float) ($selectedRider->salary ?? 0);
                if ($gross <= 0) {
                    $gross = (float) $defaultMonthlySalary;
                }

                $perDay = $gross / $daysInMonth;

                $attendanceMap = RiderAttendance::query()
                    ->where('rider_id', $selectedRiderId)
                    ->whereDate('attendance_date', '>=', $fromDate)
                    ->whereDate('attendance_date', '<=', $toDate)
                    ->get()
                    ->keyBy(function ($a) {
                        return Carbon::parse($a->attendance_date)->toDateString();
                    });

                $present = 0; $half = 0; $leave = 0; $absent = 0; $notMarked = 0;
                $payableUnits = 0.0;
                $totalPay = 0.0;

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $date = $start->copy()->day($d)->toDateString();
                    $rec = $attendanceMap->get($date);

                    $status = $rec->status ?? 'not_marked';

                    if ($status === 'present') $present++;
                    elseif ($status === 'half_day') $half++;
                    elseif ($status === 'leave') $leave++;
                    elseif ($status === 'absent') $absent++;
                    else $notMarked++;

                    $weight = $weights[$status] ?? 0.0;
                    $dayPay = round($perDay * $weight, 2);

                    $payableUnits += $weight;
                    $totalPay += $dayPay;

                    $dayRows[] = [
                        'date' => $date,
                        'status' => $status,
                        'check_in' => $rec->check_in_time ?? null,
                        'check_out' => $rec->check_out_time ?? null,
                        'working_minutes' => $rec->working_minutes ?? null,
                        'day_pay' => $dayPay,
                    ];
                }

                $totalPay = round($totalPay, 2);

                $riderTotals = [
                    'gross' => round($gross, 2),
                    'per_day' => round($perDay, 2),
                    'present' => $present,
                    'half_day' => $half,
                    'leave' => $leave,
                    'absent' => $absent,
                    'not_marked' => $notMarked,
                    'payable_units' => round($payableUnits, 2),
                    'payable' => $totalPay,
                    'deduction' => round(max(0, $gross - $totalPay), 2),
                ];
            }
        }

        if (!$selectedRiderId && $riders->count() > 0) {
            $selectedRiderId = (string) $riders->first()->rider_id;
        }

        return view('admin.rider-salary.index', [
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'daysInMonth' => $daysInMonth,
            'defaultMonthlySalary' => $defaultMonthlySalary,

            'riders' => $riders,
            'selectedRiderId' => $selectedRiderId,
            'selectedRider' => $selectedRider,
            'allRiderSalary' => $allRiderSalary,
            'dayRows' => $dayRows,
            'riderTotals' => $riderTotals,

            // NEW
            'payrollSummary' => $payrollSummary,
        ]);
    }
}
