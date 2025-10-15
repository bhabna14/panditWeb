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
        // Week selection (defaults Mon→Sun current week)
        $start = $request->filled('start')
            ? Carbon::parse($request->input('start'))->startOfDay()
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $end   = $request->filled('end')
            ? Carbon::parse($request->input('end'))->endOfDay()
            : (clone $start)->endOfWeek(Carbon::SUNDAY)->endOfDay();

        // Build day skeleton
        $period = CarbonPeriod::create($start, $end);
        $days   = [];
        foreach ($period as $d) {
            $days[$d->toDateString()] = [
                'date'     => $d->toDateString(),
                'dow'      => $d->format('l'),
                'finance'  => ['income' => 0, 'expenditure' => 0],
                'customer' => ['renew' => 0, 'new' => 0, 'pause' => 0, 'customize' => 0],
                'vendors'  => [],   // vendor name => amt
                'pickup'   => [],   // rider name  => amt
                'riders'   => [],   // rider name  => delivered count
                'total_delivery' => 0,
            ];
        }

        /* ================= Finance ================= */

        // Income (flower_payments.paid_amount)
        $payments = FlowerPayment::query()
            ->select([
                DB::raw("DATE(created_at) as d"),
                DB::raw("SUM(paid_amount) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->get();

        foreach ($payments as $row) {
            if (isset($days[$row->d])) $days[$row->d]['finance']['income'] = (float)$row->amt;
        }

        // Expenditure (vendor pickup: total_price on pickup_date)
        $expend = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                DB::raw("SUM(total_price) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('d')
            ->get();

        foreach ($expend as $row) {
            if (isset($days[$row->d])) $days[$row->d]['finance']['expenditure'] = (float)$row->amt;
        }

        /* ================= Customer ================= */

        // New subscriptions (start_date)
        $newSubs = Subscription::query()
            ->select([DB::raw("DATE(start_date) as d"), DB::raw("COUNT(*) as c")])
            ->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('d')->get();
        foreach ($newSubs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['new'] = (int)$row->c;

        // Renewals (new_date)
        $renewSubs = Subscription::query()
            ->select([DB::raw("DATE(new_date) as d"), DB::raw("COUNT(*) as c")])
            ->whereNotNull('new_date')
            ->whereBetween('new_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('d')->get();
        foreach ($renewSubs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['renew'] = (int)$row->c;

        // Pauses
        if (class_exists(SubscriptionPauseResumeLog::class)) {
            $pauses = SubscriptionPauseResumeLog::query()
                ->select([DB::raw("DATE(created_at) as d"), DB::raw("COUNT(*) as c")])
                ->where('action', 'paused')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('d')->get();
            foreach ($pauses as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['pause'] = (int)$row->c;
        } else {
            $pauses = Subscription::query()
                ->select([DB::raw("DATE(pause_start_date) as d"), DB::raw("COUNT(*) as c")])
                ->whereNotNull('pause_start_date')
                ->whereBetween('pause_start_date', [$start->toDateString(), $end->toDateString()])
                ->groupBy('d')->get();
            foreach ($pauses as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['pause'] = (int)$row->c;
        }

        // Customize Order (if tracked)
        if (class_exists(DeliveryCustomizeHistory::class)) {
            $customs = DeliveryCustomizeHistory::query()
                ->select([DB::raw("DATE(created_at) as d"), DB::raw("COUNT(*) as c")])
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('d')->get();
            foreach ($customs as $row) if (isset($days[$row->d])) $days[$row->d]['customer']['customize'] = (int)$row->c;
        }

        /* ================= Lookups (no joins → no collation errors) ================= */

        // ID → Name maps
        $vendorMap = FlowerVendor::query()->pluck('vendor_name', 'vendor_id')->toArray();
        $riderMap  = RiderDetails::query()->pluck('rider_name', 'rider_id')->toArray();

        // Vendor report: aggregate by date + vendor_id
        $vendorPaid = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                'vendor_id',
                DB::raw("SUM(total_price) as amt"),
            ])
            ->where('payment_status', 'paid')
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('d', 'vendor_id')
            ->get();

        $allVendors = [];
        foreach ($vendorPaid as $row) {
            $name = $vendorMap[$row->vendor_id] ?? $row->vendor_id; // fallback to ID if name missing
            $allVendors[$name] = true;
            if (isset($days[$row->d])) $days[$row->d]['vendors'][$name] = (float)$row->amt;
        }
        $allVendors = array_keys($allVendors);

        // Flower pickup by rider: aggregate by date + rider_id
        $pickupByRider = FlowerPickupDetails::query()
            ->select([
                DB::raw("DATE(pickup_date) as d"),
                'rider_id',
                DB::raw("SUM(total_price) as amt"),
            ])
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('d', 'rider_id')
            ->get();

        $pickupRiders = [];
        foreach ($pickupByRider as $row) {
            $name = $riderMap[$row->rider_id] ?? $row->rider_id;
            $pickupRiders[$name] = true;
            if (isset($days[$row->d])) $days[$row->d]['pickup'][$name] = (float)$row->amt;
        }
        $pickupRiders = array_keys($pickupRiders);

        // Deliveries per rider: aggregate by date + rider_id
        $deliv = DeliveryHistory::query()
            ->select([
                DB::raw("DATE(delivery_time) as d"),
                'rider_id',
                DB::raw("COUNT(*) as c"),
            ])
            ->where('delivery_status', 'delivered')
            ->whereBetween('delivery_time', [$start, $end])
            ->groupBy('d', 'rider_id')
            ->get();

        $deliveryRiders = [];
        foreach ($deliv as $row) {
            $name = $riderMap[$row->rider_id] ?? $row->rider_id;
            $deliveryRiders[$name] = true;
            if (isset($days[$row->d])) {
                $days[$row->d]['riders'][$name] = (int)$row->c;
                $days[$row->d]['total_delivery'] += (int)$row->c;
            }
        }
        $deliveryRiders = array_keys($deliveryRiders);

        // Order columns
        sort($allVendors);
        sort($pickupRiders);
        sort($deliveryRiders);

        /* ================= Totals ================= */
        $totals = [
            'income'      => 0,
            'expenditure' => 0,
            'renew'       => 0,
            'new'         => 0,
            'pause'       => 0,
            'customize'   => 0,
            'vendors'     => array_fill_keys($allVendors, 0.0),
            'pickup'      => array_fill_keys($pickupRiders, 0.0),
            'riders'      => array_fill_keys($deliveryRiders, 0),
            'total_delivery' => 0,
        ];

        foreach ($days as $row) {
            $totals['income']      += $row['finance']['income'];
            $totals['expenditure'] += $row['finance']['expenditure'];
            $totals['renew']       += $row['customer']['renew'];
            $totals['new']         += $row['customer']['new'];
            $totals['pause']       += $row['customer']['pause'];
            $totals['customize']   += $row['customer']['customize'];
            foreach ($allVendors as $v)   $totals['vendors'][$v] += $row['vendors'][$v] ?? 0;
            foreach ($pickupRiders as $r) $totals['pickup'][$r] += $row['pickup'][$r] ?? 0;
            foreach ($deliveryRiders as $r) $totals['riders'][$r] += $row['riders'][$r] ?? 0;
            $totals['total_delivery'] += $row['total_delivery'];
        }

        return view('admin.reports.weekly-reports', [
            'start'          => $start,
            'end'            => $end,
            'days'           => $days,
            'vendorColumns'  => $allVendors,
            'pickupColumns'  => $pickupRiders,
            'deliveryCols'   => $deliveryRiders,
            'totals'         => $totals,
        ]);
    }
}
