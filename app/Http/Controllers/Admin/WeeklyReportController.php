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
use App\Models\FlowerRequest;
use App\Models\OfficeFund;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int)($request->input('year', Carbon::now()->year));
        $month = (int)($request->input('month', Carbon::now()->month));

        $tz = $request->input('tz', config('app.timezone', 'UTC'));
        $tzOffset = Carbon::now($tz)->format('P'); // +05:30 etc.

        $monthStart = Carbon::createFromDate($year, $month, 1, $tz)->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth()->endOfDay();

        $monthStartUtc = $monthStart->clone()->setTimezone('UTC');
        $monthEndUtc   = $monthEnd->clone()->setTimezone('UTC');

        $dateExpr = "DATE(CONVERT_TZ(created_at, '+00:00', '$tzOffset'))";

        // ---- Day skeleton
        $days = [];
        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $d) {
            $dateKey = $d->toDateString();
            $days[$dateKey] = [
                'date'     => $dateKey,
                'dow'      => $d->format('l'),
                'finance'  => [
                    'subscription_income' => 0,
                    'customize_income'    => 0,
                    'income_total'        => 0,

                    // For modal
                    // each item: ['user_id'=>..., 'name'=>..., 'amt'=>...]
                    'subscription_income_users' => [],
                    'customize_income_users'    => [],

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

        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        /* ================= Finance: INCOME (Split totals) ================= */

        // Subscription income: flower_payments joined with subscriptions
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
                $days[$row->d]['finance']['subscription_income'] = (float)$row->amt;
            }
        }

        // Customize income: paid payments that are NOT linked to subscriptions
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
                $days[$row->d]['finance']['customize_income'] = (float)$row->amt;
            }
        }

        foreach ($days as $k => $row) {
            $sub = $row['finance']['subscription_income'] ?? 0;
            $cus = $row['finance']['customize_income'] ?? 0;
            $days[$k]['finance']['income_total'] = $sub + $cus;
        }

        /* ================= INCOME USER LIST (for modal) ================= */

        // Per-day subscription users list (grouped by day + user)
        $subUsersRows = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->join('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('users as u', 'u.userid', '=', 'fp.user_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                'fp.user_id as user_id',
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw("SUM(fp.paid_amount) as amt"),
            ])
            ->where('fp.payment_status', 'paid')
            ->whereBetween('fp.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d', 'fp.user_id', 'u.name')
            ->orderBy('d')
            ->orderByDesc('amt')
            ->get();

        // Per-day customize users list
        $custUsersRows = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('users as u', 'u.userid', '=', 'fp.user_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                'fp.user_id as user_id',
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw("SUM(fp.paid_amount) as amt"),
            ])
            ->whereNull('s.order_id')
            ->where('fp.payment_status', 'paid')
            ->whereBetween('fp.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d', 'fp.user_id', 'u.name')
            ->orderBy('d')
            ->orderByDesc('amt')
            ->get();

        foreach ($subUsersRows as $r) {
            if (!isset($days[$r->d])) continue;
            $days[$r->d]['finance']['subscription_income_users'][] = [
                'user_id' => $r->user_id,
                'name'    => $r->name,
                'amt'     => (float)$r->amt,
            ];
        }

        foreach ($custUsersRows as $r) {
            if (!isset($days[$r->d])) continue;
            $days[$r->d]['finance']['customize_income_users'][] = [
                'user_id' => $r->user_id,
                'name'    => $r->name,
                'amt'     => (float)$r->amt,
            ];
        }

        /* ================= Finance: EXPENDITURE ================= */

        $expend = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                DB::raw("SUM(total_price) as amt"),
            ])
            ->whereBetween('pickup_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d')
            ->get();

        foreach ($expend as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['expenditure'] = (float)$row->amt;
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
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d')
            ->get();

        foreach ($vendorFund as $row) {
            if (isset($days[$row->d])) {
                $days[$row->d]['finance']['vendor_fund'] = (float)$row->amt;
            }
        }

        foreach ($days as $k => $row) {
            $vf  = $row['finance']['vendor_fund'] ?? 0;
            $exp = $row['finance']['expenditure'] ?? 0;
            $days[$k]['finance']['available_balance'] = $vf - $exp;
        }

        /* ================= Customer: NEW / RENEW ================= */

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
                $days[$row->d]['customer']['new'] = (int)$row->c;
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
                $days[$row->d]['customer']['renew'] = (int)$row->c;
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
                    $days[$row->d]['customer']['pause'] = (int)$row->c;
                }
            }
        } else {
            $pauses = Subscription::query()
                ->select([
                    DB::raw("DATE(pause_start_date) as d"),
                    DB::raw("COUNT(*) as c")
                ])
                ->whereNotNull('pause_start_date')
                ->whereBetween('pause_start_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->groupBy('d')
                ->get();

            foreach ($pauses as $row) {
                if (isset($days[$row->d])) {
                    $days[$row->d]['customer']['pause'] = (int)$row->c;
                }
            }
        }

        /* ================= Customer: CUSTOMIZE ================= */

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
                $days[$row->d]['customer']['customize'] = (int)$row->c;
            }
        }

        /* ================= Vendor daily ================= */

        $vendorPaid = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                'vendor_id',
                DB::raw("SUM(total_price) as amt"),
            ])
            ->whereBetween('pickup_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('d', 'vendor_id')
            ->get();

        $vendorColumnsSet = [];
        foreach ($vendorPaid as $row) {
            $name = $vendorMap[$row->vendor_id] ?? $row->vendor_id;
            $vendorColumnsSet[$name] = true;
            if (isset($days[$row->d])) {
                $days[$row->d]['vendors'][$name] = (float)$row->amt;
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
                $days[$row->d]['riders'][$name] = (int)$row->c;
                $days[$row->d]['total_delivery'] += (int)$row->c;
            }
        }

        $deliveryCols = array_keys($deliveryColsSet);
        sort($deliveryCols);

        /* ================= Split into weeks + week totals ================= */

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
                $weekDays[$key] = $days[$key];
            }

            $weekTotals = [
                'subscription_income' => 0,
                'customize_income'    => 0,
                'income_total'        => 0,

                // for modal
                'subscription_income_users' => [],
                'customize_income_users'    => [],

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

            $weekSubUsers = [];
            $weekCusUsers = [];

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

                $weekTotals['total_delivery'] += (int)($row['total_delivery'] ?? 0);

                // Aggregate subscription users across week
                foreach (($row['finance']['subscription_income_users'] ?? []) as $u) {
                    $uid = (string)($u['user_id'] ?? '');
                    if ($uid === '') continue;

                    if (!isset($weekSubUsers[$uid])) {
                        $weekSubUsers[$uid] = ['user_id' => $u['user_id'], 'name' => $u['name'], 'amt' => 0.0];
                    }
                    $weekSubUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);
                }

                // Aggregate customize users across week
                foreach (($row['finance']['customize_income_users'] ?? []) as $u) {
                    $uid = (string)($u['user_id'] ?? '');
                    if ($uid === '') continue;

                    if (!isset($weekCusUsers[$uid])) {
                        $weekCusUsers[$uid] = ['user_id' => $u['user_id'], 'name' => $u['name'], 'amt' => 0.0];
                    }
                    $weekCusUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);
                }
            }

            $weekTotals['available_balance'] = $weekTotals['vendor_fund'] - $weekTotals['expenditure'];

            $weekSubUsersList = array_values($weekSubUsers);
            usort($weekSubUsersList, fn($a, $b) => $b['amt'] <=> $a['amt']);

            $weekCusUsersList = array_values($weekCusUsers);
            usort($weekCusUsersList, fn($a, $b) => $b['amt'] <=> $a['amt']);

            $weekTotals['subscription_income_users'] = $weekSubUsersList;
            $weekTotals['customize_income_users']    = $weekCusUsersList;

            // show only vendors used in this week
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

        /* ================= Month totals ================= */

        $monthTotals = [
            'subscription_income' => 0,
            'customize_income'    => 0,
            'income_total'        => 0,

            // for modal
            'subscription_income_users' => [],
            'customize_income_users'    => [],

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

        $monthSubUsers = [];
        $monthCusUsers = [];

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
            $monthTotals['total_delivery'] += (int)($row['total_delivery'] ?? 0);

            foreach (($row['finance']['subscription_income_users'] ?? []) as $u) {
                $uid = (string)($u['user_id'] ?? '');
                if ($uid === '') continue;

                if (!isset($monthSubUsers[$uid])) {
                    $monthSubUsers[$uid] = ['user_id' => $u['user_id'], 'name' => $u['name'], 'amt' => 0.0];
                }
                $monthSubUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);
            }

            foreach (($row['finance']['customize_income_users'] ?? []) as $u) {
                $uid = (string)($u['user_id'] ?? '');
                if ($uid === '') continue;

                if (!isset($monthCusUsers[$uid])) {
                    $monthCusUsers[$uid] = ['user_id' => $u['user_id'], 'name' => $u['name'], 'amt' => 0.0];
                }
                $monthCusUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);
            }
        }

        $monthTotals['available_balance'] = $monthTotals['vendor_fund'] - $monthTotals['expenditure'];

        $monthSubUsersList = array_values($monthSubUsers);
        usort($monthSubUsersList, fn($a, $b) => $b['amt'] <=> $a['amt']);

        $monthCusUsersList = array_values($monthCusUsers);
        usort($monthCusUsersList, fn($a, $b) => $b['amt'] <=> $a['amt']);

        $monthTotals['subscription_income_users'] = $monthSubUsersList;
        $monthTotals['customize_income_users']    = $monthCusUsersList;

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

    /**
     * Builds HTML tooltip for income (names only).
     * Kept for compatibility; modal uses *_income_users arrays.
     * $users = [['user_id' => .., 'name' => .., 'amt' => ..], ...]
     */
    private function buildIncomePopoverHtml(string $title, array $users, float $totalAmt): string
    {
        $safeTitle = e($title);
        $totalUsers = count($users);

        $usersTop = array_slice($users, 0, 25);

        $html  = "<div class='tt-head'>{$safeTitle}</div>";
        $html .= "<div class='tt-meta'>Customers: <b>{$totalUsers}</b></div>";

        if ($totalUsers === 0) {
            $html .= "<div class='tt-empty'>No paid payments found.</div>";
            return $html;
        }

        $html .= "<div class='tt-scroll'>";
        foreach ($usersTop as $u) {
            $name = e($u['name'] ?? '-');
            $uid  = e((string)($u['user_id'] ?? ''));

            $html .= "
                <div class='tt-row'>
                    <div class='tt-name'>{$name}</div>
                    <div class='tt-id'>#{$uid}</div>
                </div>
            ";
        }

        if ($totalUsers > 25) {
            $html .= "<div class='tt-more'>Showing top 25 customers.</div>";
        }

        $html .= "</div>";
        return $html;
    }
}
