<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\FlowerRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TomorrowSubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow()->startOfDay();

        // Eager-load for subscriptions
        $withSubs = [
            'users',                    // mobile_number, email, custom PK `userid`
            'users.addressDetails',     // default address
            'order',                    // shipping_* fields (if present)
            'order.rider:id,rider_id,rider_name',
            'flowerProducts:product_id,name',
            'latestPaidPayment',        // payment_status = 'paid'
            'flowerPayments',           // fallback 'status'='paid'
        ];

        // Eager-load for customize orders (FlowerRequest)
        $withRequests = [
            'user:userid,name,mobile_number,email',
            'address',                                  // show address
            'flowerProduct:product_id,name',
            'order:id,order_id,request_id,rider_id',    // may be null if not converted to order
            'order.rider:id,rider_id,rider_name',
        ];

        // Exclude terminal statuses for subs
        $excludeStatuses = ['expired', 'dead'];

        // Helper: hide "pending, starts today, unpaid"
        $shouldHide = function ($sub) use ($today) {
            if (strtolower($sub->status ?? '') !== 'pending') {
                return false;
            }
            $startsToday = $sub->start_date ? Carbon::parse($sub->start_date)->isSameDay($today) : false;
            if (!$startsToday) return false;

            $hasPaid = !empty($sub->latestPaidPayment);
            if (!$hasPaid && $sub->relationLoaded('flowerPayments')) {
                $hasPaid = $sub->flowerPayments->contains(function ($p) {
                    $ps = strtolower((string)($p->payment_status ?? ''));
                    $s  = strtolower((string)($p->status ?? ''));
                    return $ps === 'paid' || $s === 'paid';
                });
            }
            return !$hasPaid;
        };

        /* ===================== DATASETS ===================== */

        // A) Tomorrow Delivery (active tomorrow)
        $activeTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'paused', 'pending'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $tomorrow->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tomorrow->toDateString())
            ->get()
            // exclude subs paused on that day
            ->filter(function ($s) use ($tomorrow) {
                if ($s->pause_start_date && $s->pause_end_date) {
                    $ps = Carbon::parse($s->pause_start_date)->startOfDay();
                    $pe = Carbon::parse($s->pause_end_date)->endOfDay();
                    if ($ps->lte($tomorrow) && $pe->gte($tomorrow)) {
                        return false;
                    }
                }
                return true;
            })
            ->reject($shouldHide)
            ->values();

        // B) Tomorrow Customize Orders (from FlowerRequest)
        $customizeTomorrow = FlowerRequest::with($withRequests)
            ->whereDate('date', '=', $tomorrow->toDateString())
            ->get();

        // C) Pause → Active from Tomorrow (pause ends today)
        // With your inclusive pause logic, if pause_end_date >= tomorrow they're still paused tomorrow.
        // So "active from tomorrow" means pause_end_date == today.
        $resumingTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->whereNotNull('pause_end_date')
            ->whereDate('pause_end_date', '=', $today->toDateString())
            ->get()
            ->reject($shouldHide)
            ->values();

        /* ===================== MAPPERS ===================== */

        $mapSub = function ($s) {
            $user    = $s->users;
            $order   = $s->order;
            $product = $s->flowerProducts;

            // Build address: order shipping_* first, else user's default address
            $address = '';
            if ($order) {
                $parts = [];
                foreach ([
                    'shipping_name','shipping_address','shipping_street','shipping_area',
                    'shipping_city','shipping_state','shipping_pincode','shipping_zip',
                ] as $f) {
                    if (isset($order->$f) && filled($order->$f)) $parts[] = $order->$f;
                }
                $address = trim(implode(', ', array_unique(array_filter($parts))));
            }
            if (!$address && $user && $user->addressDetails) {
                $ad = $user->addressDetails;
                $chunks = array_filter([
                    $ad->apartment_flat_plot ?? null,
                    $ad->apartment_name ?? null,
                    $ad->landmark ?? null,
                    $ad->area ?? null,
                    $ad->city ?? null,
                    $ad->state ?? null,
                    $ad->pincode ?? null,
                ]);
                $address = trim(implode(', ', $chunks));
            }

            $aptName   = ($user && $user->addressDetails) ? ($user->addressDetails->apartment_name ?? '') : '';
            $rider     = $order?->rider;
            $riderName = $rider?->rider_name ?? '—';

            return [
                'subscription_id' => $s->subscription_id,
                'order_id'        => $s->order_id,
                'status'          => $s->status,
                'is_active'       => (int) $s->is_active,
                'start_date'      => $s->start_date ? (string) $s->start_date : null,
                'end_date'        => $s->end_date ? (string) $s->end_date : null,
                'new_date'        => $s->new_date ? (string) $s->new_date : null,
                'pause_start'     => $s->pause_start_date ? (string) $s->pause_start_date : null,
                'pause_end'       => $s->pause_end_date ? (string) $s->pause_end_date : null,

                'customer'        => $user?->name ?? '—',
                'phone'           => $user?->mobile_number ?? null,
                'email'           => $user?->email ?? null,

                'product'         => $product?->name ?? '—',
                'address'         => $address ?: '—',
                'apartment_name'  => $aptName,

                'rider_id'        => $rider?->rider_id ?? null,
                'rider_name'      => $riderName,
            ];
        };

        $mapRequest = function (FlowerRequest $fr) {
            $user    = $fr->user;
            $addr    = $fr->address;
            $product = $fr->flowerProduct;
            $order   = $fr->order;             // may be null
            $rider   = $order?->rider;

            // Build address from request address
            $chunks = array_filter([
                $addr?->apartment_flat_plot ?? null,
                $addr?->apartment_name ?? null,
                $addr?->landmark ?? null,
                $addr?->area ?? null,
                $addr?->city ?? null,
                $addr?->state ?? null,
                $addr?->pincode ?? null,
            ]);
            $address = trim(implode(', ', $chunks));

            return [
                'request_id'   => $fr->request_id,
                'order_id'     => $order?->order_id,
                'status'       => $fr->status ?? '—',
                'date'         => $fr->date ? (string)$fr->date : null,
                'time'         => $fr->time ? (string)$fr->time : null,

                'customer'     => $user?->name ?? '—',
                'phone'        => $user?->mobile_number ?? null,
                'email'        => $user?->email ?? null,

                'product'      => $product?->name ?? '—',
                'address'      => $address ?: '—',
                'apartment_name' => $addr?->apartment_name ?? '',

                'rider_id'     => $rider?->rider_id ?? null,
                'rider_name'   => $rider?->rider_name ?? '—',
            ];
        };

        $data = [
            'today'              => $today->toDateString(),
            'tomorrow'           => $tomorrow->toDateString(),

            // Three sections only
            'activeTomorrow'     => $activeTomorrow->map($mapSub)->all(),       // "Tomorrow Delivery"
            'customizeTomorrow'  => $customizeTomorrow->map($mapRequest)->all(),// "Tomorrow Customize Orders"
            'resumingTomorrow'   => $resumingTomorrow->map($mapSub)->all(),     // "Pause → Active (from Tomorrow)"
        ];

        return view('admin.reports.tomorrow-subscriptions', $data);
    }
}
