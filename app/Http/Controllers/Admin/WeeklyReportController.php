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
use App\Models\FlowerRequest; // for customize orders
use App\Models\OfficeFund;    // vendor fund received (Office Fund)

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        // ---- Month / Year selection (defaults to current month)
        $year  = (int)($request->input('year', Carbon::now()->year));
        $month = (int)($request->input('month', Carbon::now()->month));

        // Timezone handling (for day grouping)
        $tz = $request->input('tz', config('app.timezone', 'UTC'));
        $tzOffset = Carbon::now($tz)->format('P'); // e.g. +05:30

        $monthStart = Carbon::createFromDate($year, $month, 1, $tz)->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth()->endOfDay();

        // Convert to UTC for whereBetween on UTC timestamps
        $monthStartUtc = $monthStart->clone()->setTimezone('UTC');
        $monthEndUtc   = $monthEnd->clone()->setTimezone('UTC');

        // Expression for DATE by timezone (works without MySQL timezone tables using numeric offset)
        $dateExpr = "DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset'))";

        // ---- Build day skeleton for entire month
        $days = [];
        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $d) {
            $dateKey = $d->toDateString();
            $days[$dateKey] = [
                'date'     => $dateKey,
                'dow'      => $d->format('l'),
                'finance'  => [
                    'income'            => 0,  // from FlowerPayment
                    'expenditure'       => 0,  // Purch from FlowerPickupDetails.total_price
                    'vendor_fund'       => 0,  // from OfficeFund
                    'available_balance' => 0,  // vendor_fund - expenditure
                ],
                'customer' => [
                    'renew'     => 0,
                    'new'       => 0,
                    'pause'     => 0,
                    'customize' => 0
                ],
                'vendors'  => [],   // vendor name => total paid to vendor that day
                'riders'   => [],   // rider name  => delivered count that day
                'total_delivery' => 0,
            ];
        }

        // ---- Lookups
        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        /* ================= Finance ================= */

        // Income per day (by created_at in UTC → converted to local date)
        $payments = FlowerPayment::query()
            ->select([
                DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset')) as d"),
                DB::raw("SUM(paid_amount) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d')
            ->get();

        foreach ($payments as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['income'] = (float) $row->amt;
            }
        }

        // PURCHASE / EXPENDITURE per day:
        // Using FlowerPickupDetails.total_price grouped by pickup_date
        // NOTE: NO payment_status filter – counts all purchases.
        $expend = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                DB::raw("SUM(total_price) as amt"),
            ])
            ->whereBetween('pickup_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('d')
            ->get();

        foreach ($expend as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['expenditure'] = (float) $row->amt;
            }
        }

        // NEW: Vendor Fund (OfficeFund) per day
        // Use same "active" scope as manage-office-fund so deleted/void entries are excluded.
        $vendorFund = OfficeFund::query()
            ->active()
            ->select([
                DB::raw("DATE(date) as d"),
                DB::raw("SUM(amount) as amt"),
            ])
            ->whereBetween('date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->groupBy('d')
            ->get();

        foreach ($vendorFund as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['vendor_fund'] = (float) $row->amt;
            }
        }

        // Daily available balance = vendor_fund - expenditure
        foreach ($days as $k => $row) {
            $vf  = $row['finance']['vendor_fund'] ?? 0;
            $exp = $row['finance']['expenditure'] ?? 0;
            $days[$k]['finance']['available_balance'] = $vf - $exp;
        }

        /* ================= Customer: NEW & RENEW ================= */

        // NEW subscriptions: Users who have exactly ONE subscription overall,
        // and that subscription's created_at date (in tz) is that day.
        $firstTimeUserIds = Subscription::query()
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) = 1');

        $newPerDay = Subscription::query()
            ->select([
                DB::raw("$dateExpr as d"),
                DB::raw("COUNT(*) as c"),
            ])
            ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
            ->whereIn('user_id', $firstTimeUserIds)
            ->groupBy('d')
            ->get();

        foreach ($newPerDay as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['customer']['new'] = (int) $row->c;
            }
        }

        // RENEW subscriptions: order_id that appears more than once across subscriptions (renewed),
        // counted by created_at date.
        $renewOrderIds = Subscription::query()
            ->select('order_id')
            ->groupBy('order_id')
            ->havingRaw('COUNT(order_id) > 1');

        $renewPerDay = Subscription::query()
            ->select([
                DB::raw("$dateExpr as d"),
                DB::raw("COUNT(*) as c"),
            ])
            ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
            ->whereIn('order_id', $renewOrderIds)
            ->groupBy('d')
            ->get();

        foreach ($renewPerDay as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['customer']['renew'] = (int) $row->c;
            }
        }

        /* ================= Customer: PAUSE ================= */
        if (class_exists(SubscriptionPauseResumeLog::class)) {
            $pauses = SubscriptionPauseResumeLog::query()
                ->select([
                    DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset')) as d"),
                    DB::raw("COUNT(*) as c")
                ])
                ->where('action', 'paused')
                ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
                ->groupBy('d')
                ->get();

            foreach ($pauses as $row) {
                if (isset($days[$row->d])) {
                    $days[$row->d]['customer']['pause'] = (int) $row->c;
                }
            }
        } else {
            $pauses = Subscription::query()
                ->select([
                    DB::raw("DATE(pause_start_date) as d"),
                    DB::raw("COUNT(*) as c")
                ])
                ->whereNotNull('pause_start_date')
                ->whereBetween('pause_start_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString()
                ])
                ->groupBy('d')
                ->get();

            foreach ($pauses as $row) {
                if (isset($days[$row->d])) {
                    $days[$row->d]['customer']['pause'] = (int) $row->c;
                }
            }
        }

        /* ================= Customer: CUSTOMIZE (FlowerRequest) ================= */
        $customs = FlowerRequest::query()
            ->select([
                DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset')) as d"),
                DB::raw("COUNT(*) as c"),
            ])
            ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d')
            ->get();

        foreach ($customs as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['customer']['customize'] = (int) $row->c;
            }
        }

        /* ================= Vendor daily (vendor-wise paid per day) ================= */
        $vendorPaid = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                'vendor_id',
                DB::raw("SUM(total_price) as amt"),
            ])
            ->whereBetween('pickup_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString()
            ])
            ->groupBy('d', 'vendor_id')
            ->get();

        $vendorColumnsSet = [];
        foreach ($vendorPaid as $row) {
            $name = $vendorMap[$row->vendor_id] ?? $row->vendor_id;
            $vendorColumnsSet[$name] = true;
            if (isset($days[$row->d])) {
                $days[$row->d]['vendors'][$name] = (float) $row->amt;
            }
        }
        // all vendors that appear at least once in the month
        $vendorColumns = array_keys($vendorColumnsSet);
        sort($vendorColumns);

        /* ================= Deliveries per rider (counts, by created_at date) ================= */
        $deliv = DeliveryHistory::query()
            ->select([
                DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset')) as d"),
                'rider_id',
                DB::raw("COUNT(*) as c"),
            ])
            ->where('delivery_status', 'delivered')
            ->whereBetween('created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d', 'rider_id')
            ->get();

        $deliveryColsSet = [];
        foreach ($deliv as $row) {
            $name = $riderMap[$row->rider_id] ?? $row->rider_id;
            $deliveryColsSet[$name] = true;

            if (isset($days[$row->d])) {
                $days[$row->d]['riders'][$name] = (int) $row->c;
                $days[$row->d]['total_delivery'] += (int) $row->c;
            }
        }
        $deliveryCols = array_keys($deliveryColsSet);
        sort($deliveryCols);

        // ---- Split into weeks (Mon→Sun) using display timezone
        $weeks = [];
        $cursor    = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $endCursor = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        while ($cursor->lte($endCursor)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->endOfWeek(Carbon::SUNDAY);

            $rangeStart = $weekStart->lt($monthStart) ? $monthStart->copy() : $weekStart->copy();
            $rangeEnd   = $weekEnd->gt($monthEnd) ? $monthEnd->copy() : $weekEnd->copy();

            // Collect day rows for this week
            $weekDays = [];
            foreach (CarbonPeriod::create($rangeStart, $rangeEnd) as $d) {
                $key = $d->toDateString();
                $weekDays[$key] = $days[$key] ?? [
                    'date'     => $key,
                    'dow'      => $d->format('l'),
                    'finance'  => [
                        'income'            => 0,
                        'expenditure'       => 0,
                        'vendor_fund'       => 0,
                        'available_balance' => 0,
                    ],
                    'customer' => [
                        'renew'     => 0,
                        'new'       => 0,
                        'pause'     => 0,
                        'customize' => 0
                    ],
                    'vendors'  => [],
                    'riders'   => [],
                    'total_delivery' => 0,
                ];
            }

            // Week totals
            $weekTotals = [
                'income'            => 0,
                'expenditure'       => 0,
                'vendor_fund'       => 0,
                'available_balance' => 0,
                'renew'             => 0,
                'new'               => 0,
                'pause'             => 0,
                'customize'         => 0,
                'vendors'           => array_fill_keys($vendorColumns, 0.0),
                'riders'            => array_fill_keys($deliveryCols, 0),
                'total_delivery'    => 0,
            ];

            foreach ($weekDays as $row) {
                $weekTotals['income']       += $row['finance']['income'];
                $weekTotals['expenditure']  += $row['finance']['expenditure'];
                $weekTotals['vendor_fund']  += $row['finance']['vendor_fund'] ?? 0;

                $weekTotals['renew']        += $row['customer']['renew'];
                $weekTotals['new']          += $row['customer']['new'];
                $weekTotals['pause']        += $row['customer']['pause'];
                $weekTotals['customize']    += $row['customer']['customize'];

                foreach ($vendorColumns as $v) {
                    $weekTotals['vendors'][$v] += $row['vendors'][$v] ?? 0;
                }
                foreach ($deliveryCols as $r) {
                    $weekTotals['riders'][$r]  += $row['riders'][$r] ?? 0;
                }

                $weekTotals['total_delivery'] += $row['total_delivery'];
            }

            // Available balance for the week (Vendor Fund - Expense)
            $weekTotals['available_balance'] = $weekTotals['vendor_fund'] - $weekTotals['expenditure'];

            // Per-week vendor columns: only vendors with >0 amount in this week
            $weekVendorColumns = [];
            foreach ($vendorColumns as $v) {
                if (($weekTotals['vendors'][$v] ?? 0) > 0) {
                    $weekVendorColumns[] = $v;
                }
            }

            $weeks[] = [
                'start'         => $rangeStart,
                'end'           => $rangeEnd,
                'days'          => $weekDays,
                'totals'        => $weekTotals,
                'vendorColumns' => $weekVendorColumns,   // dynamic vendor columns for that week
            ];

            $cursor->addWeek();
        }

        // ---- Month totals
        $monthTotals = [
            'income'            => 0,
            'expenditure'       => 0,
            'vendor_fund'       => 0,
            'available_balance' => 0,
            'renew'             => 0,
            'new'               => 0,
            'pause'             => 0,
            'customize'         => 0,
            'vendors'           => array_fill_keys($vendorColumns, 0.0),
            'riders'            => array_fill_keys($deliveryCols, 0),
            'total_delivery'    => 0,
        ];

        foreach ($days as $row) {
            $monthTotals['income']         += $row['finance']['income'];
            $monthTotals['expenditure']    += $row['finance']['expenditure'];
            $monthTotals['vendor_fund']    += $row['finance']['vendor_fund'] ?? 0;

            $monthTotals['renew']          += $row['customer']['renew'];
            $monthTotals['new']            += $row['customer']['new'];
            $monthTotals['pause']          += $row['customer']['pause'];
            $monthTotals['customize']      += $row['customer']['customize'];

            foreach ($vendorColumns as $v) {
                $monthTotals['vendors'][$v] += $row['vendors'][$v] ?? 0;
            }
            foreach ($deliveryCols as $r) {
                $monthTotals['riders'][$r]  += $row['riders'][$r] ?? 0;
            }

            $monthTotals['total_delivery'] += $row['total_delivery'];
        }

        // Month available balance
        $monthTotals['available_balance'] = $monthTotals['vendor_fund'] - $monthTotals['expenditure'];

        // ---- Month (All Days) ordered for table
        $monthDays = $days;
        ksort($monthDays);

        $years = range(Carbon::now()->year - 3, Carbon::now()->year + 1);

        return view('admin.reports.month-weeks-report', [
            'year'           => $year,
            'month'          => $month,
            'monthStart'     => $monthStart,
            'monthEnd'       => $monthEnd,
            'weeks'          => $weeks,
            'vendorColumns'  => $vendorColumns,   // full month vendor list (for month tab)
            'deliveryCols'   => $deliveryCols,
            'monthTotals'    => $monthTotals,
            'monthDays'      => $monthDays,
            'years'          => $years,
        ]);
    }
}
