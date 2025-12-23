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
                    // NEW: split income
                    'subscription_income' => 0,  // payments where order_id exists in subscriptions
                    'customize_income'    => 0,  // payments where order_id NOT in subscriptions (includes customize/non-subscription)
                    'income_total'        => 0,  // subscription_income + customize_income

                    'expenditure'         => 0,  // Purch from FlowerPickupDetails.total_price
                    'vendor_fund'         => 0,  // from OfficeFund (categories = vendor_payment)
                    'available_balance'   => 0,  // vendor_fund - expenditure
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

        // ---- Lookups
        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        /* ================= Finance: INCOME (Split) ================= */

        // 1) Subscription Income per day:
        // Paid payments JOIN subscriptions by order_id
        $subPayments = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->join('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                DB::raw("SUM(fp.paid_amount) as amt"),
            ])
            ->where('fp.payment_status', 'paid')
            ->whereBetween('fp.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d')
            ->get();

        foreach ($subPayments as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['subscription_income'] = (float) $row->amt;
            }
        }

        // 2) Customize/Non-Subscription Income per day:
        // Paid payments that DO NOT match subscriptions by order_id
        // Note: This includes any non-subscription income. If you want STRICT "customize only",
        // you can join orders + flower_requests and filter there.
        $custPayments = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                DB::raw("SUM(fp.paid_amount) as amt"),
            ])
            ->whereNull('s.order_id')
            ->where('fp.payment_status', 'paid')
            ->whereBetween('fp.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d')
            ->get();

        foreach ($custPayments as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['customize_income'] = (float) $row->amt;
            }
        }

        // Daily total income = subscription_income + customize_income
        foreach ($days as $k => $row) {
            $sub = $row['finance']['subscription_income'] ?? 0;
            $cus = $row['finance']['customize_income'] ?? 0;
            $days[$k]['finance']['income_total'] = $sub + $cus;
        }

        /* ================= Finance: PURCHASE / EXPENDITURE ================= */

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

        /* ================= Finance: Vendor Fund ================= */

        $vendorFund = OfficeFund::query()
            ->active()
            ->where('categories', 'vendor_payment')
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

        $vendorColumns = array_keys($vendorColumnsSet);
        sort($vendorColumns);

        /* ================= Deliveries per rider ================= */

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

        // ---- Split into weeks (Mon→Sun)
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
                    'finance'  => [
                        'subscription_income' => 0,
                        'customize_income'    => 0,
                        'income_total'        => 0,
                        'expenditure'         => 0,
                        'vendor_fund'         => 0,
                        'available_balance'   => 0,
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

            $weekTotals = [
                'subscription_income' => 0,
                'customize_income'    => 0,
                'income_total'        => 0,

                'expenditure'         => 0,
                'vendor_fund'         => 0,
                'available_balance'   => 0,

                'renew'               => 0,
                'new'                 => 0,
                'pause'               => 0,
                'customize'           => 0,

                'vendors'             => array_fill_keys($vendorColumns, 0.0),
                'riders'              => array_fill_keys($deliveryCols, 0),
                'total_delivery'      => 0,
            ];

            foreach ($weekDays as $row) {
                $weekTotals['subscription_income'] += (float)($row['finance']['subscription_income'] ?? 0);
                $weekTotals['customize_income']    += (float)($row['finance']['customize_income'] ?? 0);
                $weekTotals['income_total']        += (float)($row['finance']['income_total'] ?? 0);

                $weekTotals['expenditure']         += (float)($row['finance']['expenditure'] ?? 0);
                $weekTotals['vendor_fund']         += (float)($row['finance']['vendor_fund'] ?? 0);

                $weekTotals['renew']               += (int)($row['customer']['renew'] ?? 0);
                $weekTotals['new']                 += (int)($row['customer']['new'] ?? 0);
                $weekTotals['pause']               += (int)($row['customer']['pause'] ?? 0);
                $weekTotals['customize']           += (int)($row['customer']['customize'] ?? 0);

                foreach ($vendorColumns as $v) {
                    $weekTotals['vendors'][$v] += (float)($row['vendors'][$v] ?? 0);
                }
                foreach ($deliveryCols as $r) {
                    $weekTotals['riders'][$r]  += (int)($row['riders'][$r] ?? 0);
                }

                $weekTotals['total_delivery']      += (int)($row['total_delivery'] ?? 0);
            }

            $weekTotals['available_balance'] = $weekTotals['vendor_fund'] - $weekTotals['expenditure'];

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
                'vendorColumns' => $weekVendorColumns,
            ];

            $cursor->addWeek();
        }

        // ---- Month totals
        $monthTotals = [
            'subscription_income' => 0,
            'customize_income'    => 0,
            'income_total'        => 0,

            'expenditure'         => 0,
            'vendor_fund'         => 0,
            'available_balance'   => 0,

            'renew'               => 0,
            'new'                 => 0,
            'pause'               => 0,
            'customize'           => 0,

            'vendors'             => array_fill_keys($vendorColumns, 0.0),
            'riders'              => array_fill_keys($deliveryCols, 0),
            'total_delivery'      => 0,
        ];

        foreach ($days as $row) {
            $monthTotals['subscription_income'] += (float)($row['finance']['subscription_income'] ?? 0);
            $monthTotals['customize_income']    += (float)($row['finance']['customize_income'] ?? 0);
            $monthTotals['income_total']        += (float)($row['finance']['income_total'] ?? 0);

            $monthTotals['expenditure']         += (float)($row['finance']['expenditure'] ?? 0);
            $monthTotals['vendor_fund']         += (float)($row['finance']['vendor_fund'] ?? 0);

            $monthTotals['renew']               += (int)($row['customer']['renew'] ?? 0);
            $monthTotals['new']                 += (int)($row['customer']['new'] ?? 0);
            $monthTotals['pause']               += (int)($row['customer']['pause'] ?? 0);
            $monthTotals['customize']           += (int)($row['customer']['customize'] ?? 0);

            foreach ($vendorColumns as $v) {
                $monthTotals['vendors'][$v] += (float)($row['vendors'][$v] ?? 0);
            }
            foreach ($deliveryCols as $r) {
                $monthTotals['riders'][$r]  += (int)($row['riders'][$r] ?? 0);
            }

            $monthTotals['total_delivery']      += (int)($row['total_delivery'] ?? 0);
        }

        $monthTotals['available_balance'] = $monthTotals['vendor_fund'] - $monthTotals['expenditure'];

        // ---- Month (All Days)
        $monthDays = $days;
        ksort($monthDays);

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
            'monthDays'      => $monthDays,
            'years'          => $years,
        ]);
    }
}
