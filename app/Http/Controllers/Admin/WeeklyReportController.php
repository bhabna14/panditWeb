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
use App\Models\OfficeFund;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        // ---- Filters (year/month) ----
        $year  = (int)($request->year ?? Carbon::now()->year);
        $month = (int)($request->month ?? Carbon::now()->month);

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth()->endOfDay();

        // Your existing timezone offset string (used in CONVERT_TZ)
        // If you store UTC in DB and want local report, keep this consistent with your system.
        $tzOffset = '+05:30';

        // For whereBetween on fp.created_at (assuming created_at stored in UTC)
        $monthStartUtc = (clone $monthStart)->setTimezone('UTC');
        $monthEndUtc   = (clone $monthEnd)->setTimezone('UTC');

        // ---- Build day skeleton ----
        $days = [];
        $period = CarbonPeriod::create($monthStart, $monthEnd);

        foreach ($period as $date) {
            $d = $date->toDateString();
            $days[$d] = [
                'date'     => $d,
                'finance'  => [
                    'subscription_income' => 0,
                    'customize_income'    => 0,
                    'income_total'        => 0,

                    // For modal (NOW includes payment_methods)
                    // each item: ['user_id'=>..., 'name'=>..., 'amt'=>..., 'payment_methods'=>...]
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
                    'customize' => 0,
                ],
                'vendors'  => [],
                'riders'   => [],
                'total_delivery' => 0,
            ];
        }

        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        $vendorColumns = array_values(array_unique(array_filter(array_values($vendorMap))));
        sort($vendorColumns);

        $deliveryCols = array_values(array_unique(array_filter(array_values($riderMap))));
        sort($deliveryCols);

        // Initialize day vendor/rider columns
        foreach ($days as $k => $row) {
            foreach ($vendorColumns as $v) $days[$k]['vendors'][$v] = 0;
            foreach ($deliveryCols as $r) $days[$k]['riders'][$r] = 0;
        }

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

        // Customize income: flower_payments NOT linked to subscriptions
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

        // Per-day subscription users list (grouped by day + user) + payment methods
        $subUsersRows = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->join('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('users as u', 'u.userid', '=', 'fp.user_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                'fp.user_id as user_id',
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw("SUM(fp.paid_amount) as amt"),
                DB::raw("GROUP_CONCAT(DISTINCT fp.payment_method ORDER BY fp.payment_method SEPARATOR ', ') as payment_methods"),
            ])
            ->where('fp.payment_status', 'paid')
            ->whereBetween('fp.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy('d', 'fp.user_id', 'u.name')
            ->orderBy('d')
            ->orderByDesc('amt')
            ->get();

        // Per-day customize users list + payment methods
        $custUsersRows = FlowerPayment::query()
            ->from('flower_payments as fp')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('users as u', 'u.userid', '=', 'fp.user_id')
            ->select([
                DB::raw("DATE(CONVERT_TZ(fp.created_at, '+00:00', '$tzOffset')) as d"),
                'fp.user_id as user_id',
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw("SUM(fp.paid_amount) as amt"),
                DB::raw("GROUP_CONCAT(DISTINCT fp.payment_method ORDER BY fp.payment_method SEPARATOR ', ') as payment_methods"),
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
                'user_id'         => $r->user_id,
                'name'            => $r->name,
                'amt'             => (float)$r->amt,
                'payment_methods' => (string)($r->payment_methods ?? ''),
            ];
        }

        foreach ($custUsersRows as $r) {
            if (!isset($days[$r->d])) continue;
            $days[$r->d]['finance']['customize_income_users'][] = [
                'user_id'         => $r->user_id,
                'name'            => $r->name,
                'amt'             => (float)$r->amt,
                'payment_methods' => (string)($r->payment_methods ?? ''),
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

        // available_balance per day
        foreach ($days as $k => $row) {
            $vf = (float)($row['finance']['vendor_fund'] ?? 0);
            $ex = (float)($row['finance']['expenditure'] ?? 0);
            $days[$k]['finance']['available_balance'] = $vf - $ex;
        }

        /* ================= Customer counts (existing logic placeholder) ================= */
        // Keep your original logic here if present in your file.
        // (Not modified for payment_method request.)

        /* ================= Vendor/Rider deliveries (existing logic placeholder) ================= */
        // Keep your original logic here if present in your file.
        // (Not modified for payment_method request.)

        /* ================= Build week blocks (Mon-Sun) ================= */

        $cursor = (clone $monthStart)->startOfWeek(Carbon::MONDAY);
        $endCursor = (clone $monthEnd)->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        while ($cursor->lte($endCursor)) {
            $rangeStart = (clone $cursor);
            $rangeEnd   = (clone $cursor)->endOfWeek(Carbon::SUNDAY);

            $weekDays = [];
            $tmp = (clone $rangeStart);
            while ($tmp->lte($rangeEnd)) {
                $key = $tmp->toDateString();
                if (isset($days[$key])) {
                    $weekDays[] = $days[$key];
                }
                $tmp->addDay();
            }

            $weekTotals = [
                'subscription_income' => 0,
                'customize_income'    => 0,
                'income_total'        => 0,

                // modal lists
                'subscription_income_users' => [],
                'customize_income_users'    => [],

                'expenditure'         => 0,
                'vendor_fund'         => 0,
                'available_balance'   => 0,

                'renew'     => 0,
                'new'       => 0,
                'pause'     => 0,
                'customize' => 0,

                'vendors'   => [],
                'riders'    => [],
                'total_delivery' => 0,
            ];

            foreach ($vendorColumns as $v) $weekTotals['vendors'][$v] = 0;
            foreach ($deliveryCols as $r) $weekTotals['riders'][$r] = 0;

            $weekSubUsers = []; // uid => ['user_id','name','amt','methods_set'=>[]]
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
                    $weekTotals['riders'][$r] += (float)($row['riders'][$r] ?? 0);
                }

                $weekTotals['total_delivery'] += (int)($row['total_delivery'] ?? 0);

                // Aggregate subscription users across week (NOW merges payment_methods)
                foreach (($row['finance']['subscription_income_users'] ?? []) as $u) {
                    $uid = (string)($u['user_id'] ?? '');
                    if ($uid === '') continue;

                    if (!isset($weekSubUsers[$uid])) {
                        $weekSubUsers[$uid] = [
                            'user_id' => $u['user_id'],
                            'name'    => $u['name'],
                            'amt'     => 0.0,
                            '_methods_set' => [],
                        ];
                    }
                    $weekSubUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);

                    $methods = (string)($u['payment_methods'] ?? '');
                    if ($methods !== '') {
                        foreach (array_map('trim', explode(',', $methods)) as $m) {
                            if ($m !== '') $weekSubUsers[$uid]['_methods_set'][$m] = true;
                        }
                    }
                }

                // Aggregate customize users across week (NOW merges payment_methods)
                foreach (($row['finance']['customize_income_users'] ?? []) as $u) {
                    $uid = (string)($u['user_id'] ?? '');
                    if ($uid === '') continue;

                    if (!isset($weekCusUsers[$uid])) {
                        $weekCusUsers[$uid] = [
                            'user_id' => $u['user_id'],
                            'name'    => $u['name'],
                            'amt'     => 0.0,
                            '_methods_set' => [],
                        ];
                    }
                    $weekCusUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);

                    $methods = (string)($u['payment_methods'] ?? '');
                    if ($methods !== '') {
                        foreach (array_map('trim', explode(',', $methods)) as $m) {
                            if ($m !== '') $weekCusUsers[$uid]['_methods_set'][$m] = true;
                        }
                    }
                }
            }

            $weekTotals['available_balance'] = $weekTotals['vendor_fund'] - $weekTotals['expenditure'];

            // finalize week user lists
            $weekSubUsersList = array_values($weekSubUsers);
            foreach ($weekSubUsersList as &$x) {
                $x['payment_methods'] = implode(', ', array_keys($x['_methods_set'] ?? []));
                unset($x['_methods_set']);
            }
            unset($x);

            $weekCusUsersList = array_values($weekCusUsers);
            foreach ($weekCusUsersList as &$x) {
                $x['payment_methods'] = implode(', ', array_keys($x['_methods_set'] ?? []));
                unset($x['_methods_set']);
            }
            unset($x);

            usort($weekSubUsersList, fn($a, $b) => ($b['amt'] ?? 0) <=> ($a['amt'] ?? 0));
            usort($weekCusUsersList, fn($a, $b) => ($b['amt'] ?? 0) <=> ($a['amt'] ?? 0));

            $weekTotals['subscription_income_users'] = $weekSubUsersList;
            $weekTotals['customize_income_users']    = $weekCusUsersList;

            // show only vendors used in this week
            $weekVendorColumns = [];
            foreach ($vendorColumns as $v) {
                $sumV = (float)($weekTotals['vendors'][$v] ?? 0);
                if ($sumV > 0) $weekVendorColumns[] = $v;
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

            'subscription_income_users' => [],
            'customize_income_users'    => [],

            'expenditure'         => 0,
            'vendor_fund'         => 0,
            'available_balance'   => 0,

            'renew'     => 0,
            'new'       => 0,
            'pause'     => 0,
            'customize' => 0,

            'vendors' => [],
            'riders'  => [],
            'total_delivery' => 0,
        ];

        foreach ($vendorColumns as $v) $monthTotals['vendors'][$v] = 0;
        foreach ($deliveryCols as $r) $monthTotals['riders'][$r] = 0;

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
                $monthTotals['riders'][$r] += (float)($row['riders'][$r] ?? 0);
            }
            $monthTotals['total_delivery'] += (int)($row['total_delivery'] ?? 0);

            // Aggregate subscription users across month (merge payment methods)
            foreach (($row['finance']['subscription_income_users'] ?? []) as $u) {
                $uid = (string)($u['user_id'] ?? '');
                if ($uid === '') continue;

                if (!isset($monthSubUsers[$uid])) {
                    $monthSubUsers[$uid] = [
                        'user_id' => $u['user_id'],
                        'name'    => $u['name'],
                        'amt'     => 0.0,
                        '_methods_set' => [],
                    ];
                }
                $monthSubUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);

                $methods = (string)($u['payment_methods'] ?? '');
                if ($methods !== '') {
                    foreach (array_map('trim', explode(',', $methods)) as $m) {
                        if ($m !== '') $monthSubUsers[$uid]['_methods_set'][$m] = true;
                    }
                }
            }

            // Aggregate customize users across month (merge payment methods)
            foreach (($row['finance']['customize_income_users'] ?? []) as $u) {
                $uid = (string)($u['user_id'] ?? '');
                if ($uid === '') continue;

                if (!isset($monthCusUsers[$uid])) {
                    $monthCusUsers[$uid] = [
                        'user_id' => $u['user_id'],
                        'name'    => $u['name'],
                        'amt'     => 0.0,
                        '_methods_set' => [],
                    ];
                }
                $monthCusUsers[$uid]['amt'] += (float)($u['amt'] ?? 0);

                $methods = (string)($u['payment_methods'] ?? '');
                if ($methods !== '') {
                    foreach (array_map('trim', explode(',', $methods)) as $m) {
                        if ($m !== '') $monthCusUsers[$uid]['_methods_set'][$m] = true;
                    }
                }
            }
        }

        $monthTotals['available_balance'] = $monthTotals['vendor_fund'] - $monthTotals['expenditure'];

        $monthSubUsersList = array_values($monthSubUsers);
        foreach ($monthSubUsersList as &$x) {
            $x['payment_methods'] = implode(', ', array_keys($x['_methods_set'] ?? []));
            unset($x['_methods_set']);
        }
        unset($x);

        $monthCusUsersList = array_values($monthCusUsers);
        foreach ($monthCusUsersList as &$x) {
            $x['payment_methods'] = implode(', ', array_keys($x['_methods_set'] ?? []));
            unset($x['_methods_set']);
        }
        unset($x);

        usort($monthSubUsersList, fn($a, $b) => ($b['amt'] ?? 0) <=> ($a['amt'] ?? 0));
        usort($monthCusUsersList, fn($a, $b) => ($b['amt'] ?? 0) <=> ($a['amt'] ?? 0));

        $monthTotals['subscription_income_users'] = $monthSubUsersList;
        $monthTotals['customize_income_users']    = $monthCusUsersList;

        $monthDays = $days;

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
            $pm   = e((string)($u['payment_methods'] ?? ''));

            $pmHtml = $pm !== '' ? "<div class='tt-pm'>{$pm}</div>" : "";

            $html .= "
                <div class='tt-row'>
                    <div class='tt-name'>{$name}</div>
                    <div class='tt-id'>#{$uid}</div>
                    {$pmHtml}
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
