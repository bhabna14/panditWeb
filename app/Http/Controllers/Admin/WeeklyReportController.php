<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\FlowerPayment;
use App\Models\FlowerPickupDetails;
use App\Models\DeliveryHistory;
use App\Models\Subscription;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\SubscriptionPauseResumeLog;
use App\Models\DeliveryCustomizeHistory;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        // --- Month / Year selection (defaults to current month)
        $year  = (int)($request->input('year', Carbon::now()->year));
        $month = (int)($request->input('month', Carbon::now()->month));

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth()->endOfDay();

        // --- Build day skeleton for entire month
        $days = [];
        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $d) {
            $days[$d->toDateString()] = [
                'date'     => $d->toDateString(),
                'dow'      => $d->format('l'),
                'finance'  => ['income' => 0, 'expenditure' => 0],
                'customer' => ['renew' => 0, 'new' => 0, 'pause' => 0, 'customize' => 0],
                'vendors'  => [],   // vendor name => total paid to vendor that day
                'riders'   => [],   // rider name  => delivered count that day
                'total_delivery' => 0,
            ];
        }

        // --- Lookups
        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        /* ================= Finance ================= */
        // Income per day
        $payments = FlowerPayment::query()
            ->select([
                DB::raw("DATE(created_at) as d"),
                DB::raw("SUM(paid_amount) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('d')
            ->get();
        foreach ($payments as $row) {
            if (isset($days[$row->d])) $days[$row->d]['finance']['income'] = (float)$row->amt;
        }

        // Expenditure per day
        $expend = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                DB::raw("SUM(total_price) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('pickup_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d')
            ->get();
        foreach ($expend as $row) {
            if (isset($days[$row->d])) $days[$row->d]['finance']['expenditure'] = (float)$row->amt;
        }

        /* ================= Customer ================= */
        // New
        $newSubs = Subscription::query()
            ->select([DB::raw("DATE(start_date) as d"), DB::raw("COUNT(*) as c")])
            ->whereBetween('start_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d')->get();
        foreach ($newSubs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['new'] = (int)$row->c;

        // Renew
        $renewSubs = Subscription::query()
            ->select([DB::raw("DATE(new_date) as d"), DB::raw("COUNT(*) as c")])
            ->whereNotNull('new_date')
            ->whereBetween('new_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d')->get();
        foreach ($renewSubs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['renew'] = (int)$row->c;

        // Pause (prefer log)
        if (class_exists(SubscriptionPauseResumeLog::class)) {
            $pauses = SubscriptionPauseResumeLog::query()
                ->select([DB::raw("DATE(created_at) as d"), DB::raw("COUNT(*) as c")])
                ->where('action', 'paused')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->groupBy('d')->get();
            foreach ($pauses as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['pause'] = (int)$row->c;
        } else {
            $pauses = Subscription::query()
                ->select([DB::raw("DATE(pause_start_date) as d"), DB::raw("COUNT(*) as c")])
                ->whereNotNull('pause_start_date')
                ->whereBetween('pause_start_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->groupBy('d')->get();
            foreach ($pauses as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['pause'] = (int)$row->c;
        }

        // Customize (if tracked)
        if (class_exists(DeliveryCustomizeHistory::class)) {
            $customs = DeliveryCustomizeHistory::query()
                ->select([DB::raw("DATE(created_at) as d"), DB::raw("COUNT(*) as c")])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->groupBy('d')->get();
            foreach ($customs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['customize'] = (int)$row->c;
        }

        /* ================= Vendor daily ================= */
        $vendorPaid = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                'vendor_id',
                DB::raw("SUM(total_price) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('pickup_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d', 'vendor_id')
            ->get();

        $vendorColumnsSet = [];
        foreach ($vendorPaid as $row) {
            $name = $vendorMap[$row->vendor_id] ?? $row->vendor_id;
            $vendorColumnsSet[$name] = true;
            if (isset($days[$row->d])) $days[$row->d]['vendors'][$name] = (float)$row->amt;
        }
        $vendorColumns = array_keys($vendorColumnsSet);
        sort($vendorColumns);

        /* ================= Rider deliveries per day ================= */
        $deliv = DeliveryHistory::query()
            ->select([
                DB::raw("DATE(created_at) as d"),
                'rider_id',
                DB::raw("COUNT(*) as c"),
            ])
            ->where('delivery_status', 'delivered')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('d', 'rider_id')
            ->get();

        $deliveryColsSet = [];
        foreach ($deliv as $row) {
            $name = $riderMap[$row->rider_id] ?? $row->rider_id;
            $deliveryColsSet[$name] = true;
            if (isset($days[$row->d])) {
                $days[$row->d]['riders'][$name] = (int)$row->c;
                $days[$row->d]['total_delivery'] += (int)$row->c;
            }
        }
        $deliveryCols = array_keys($deliveryColsSet);
        sort($deliveryCols);

        // --- Split into weeks (Mon→Sun)
        $weeks = [];
        $cursor    = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $endCursor = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        while ($cursor->lte($endCursor)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->endOfWeek(Carbon::SUNDAY);

            $rangeStart = $weekStart->lt($monthStart) ? $monthStart->copy() : $weekStart->copy();
            $rangeEnd   = $weekEnd->gt($monthEnd) ? $monthEnd->copy() : $weekEnd->copy();

            $weekDays = [];
            foreach (CarbonPeriod::create($rangeStart, $rangeEnd) as $d) {
                $key = $d->toDateString();
                $weekDays[$key] = $days[$key] ?? [
                    'date'     => $key,
                    'dow'      => $d->format('l'),
                    'finance'  => ['income' => 0, 'expenditure' => 0],
                    'customer' => ['renew' => 0, 'new' => 0, 'pause' => 0, 'customize' => 0],
                    'vendors'  => [],
                    'riders'   => [],
                    'total_delivery' => 0,
                ];
            }

            $weekTotals = [
                'income' => 0, 'expenditure' => 0,
                'renew' => 0, 'new' => 0, 'pause' => 0, 'customize' => 0,
                'vendors' => array_fill_keys($vendorColumns, 0.0),
                'riders'  => array_fill_keys($deliveryCols, 0),
                'total_delivery' => 0,
            ];
            foreach ($weekDays as $row) {
                $weekTotals['income']      += $row['finance']['income'];
                $weekTotals['expenditure'] += $row['finance']['expenditure'];
                $weekTotals['renew']       += $row['customer']['renew'];
                $weekTotals['new']         += $row['customer']['new'];
                $weekTotals['pause']       += $row['customer']['pause'];
                $weekTotals['customize']   += $row['customer']['customize'];
                foreach ($vendorColumns as $v) $weekTotals['vendors'][$v] += $row['vendors'][$v] ?? 0;
                foreach ($deliveryCols as $r)  $weekTotals['riders'][$r]  += $row['riders'][$r] ?? 0;
                $weekTotals['total_delivery'] += $row['total_delivery'];
            }

            $weeks[] = [
                'start'  => $rangeStart,
                'end'    => $rangeEnd,
                'days'   => $weekDays,
                'totals' => $weekTotals,
            ];

            $cursor->addWeek();
        }

        // --- Month totals
        $monthTotals = [
            'income'      => 0, 'expenditure' => 0,
            'renew'       => 0, 'new' => 0, 'pause' => 0, 'customize' => 0,
            'vendors'     => array_fill_keys($vendorColumns, 0.0),
            'riders'      => array_fill_keys($deliveryCols, 0),
            'total_delivery' => 0,
        ];
        foreach ($days as $row) {
            $monthTotals['income']      += $row['finance']['income'];
            $monthTotals['expenditure'] += $row['finance']['expenditure'];
            $monthTotals['renew']       += $row['customer']['renew'];
            $monthTotals['new']         += $row['customer']['new'];
            $monthTotals['pause']       += $row['customer']['pause'];
            $monthTotals['customize']   += $row['customer']['customize'];
            foreach ($vendorColumns as $v) $monthTotals['vendors'][$v] += $row['vendors'][$v] ?? 0;
            foreach ($deliveryCols as $r)  $monthTotals['riders'][$r]  += $row['riders'][$r] ?? 0;
            $monthTotals['total_delivery'] += $row['total_delivery'];
        }

        // --- NEW: Ordered list of all month days for "Month (All Days)" table
        $monthDays = $days;
        ksort($monthDays); // ensure ascending by date

        $years = range(Carbon::now()->year - 3, Carbon::now()->year + 1);

        return view('admin.reports.month-weeks-report', [
            'year'           => $year,
            'month'          => $month,
            'monthStart'     => $monthStart,
            'monthEnd'       => $monthEnd,
            'weeks'          => $weeks,
            'vendorColumns'  => $vendorColumns,
            'deliveryCols'   => $deliveryCols,
            'monthTotals'    => $monthTotals,
            'monthDays'      => $monthDays,   // <-- pass all days of month
            'years'          => $years,
        ]);
    }
}
