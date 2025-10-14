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
            'users',
            'users.addressDetails',
            'order',
            'order.rider:id,rider_id,rider_name',
            'flowerProducts:product_id,name',
            'latestPaidPayment',
            'flowerPayments',
        ];

        // Eager-load for customize orders (FlowerRequest)
        $withRequests = [
            'user:userid,name,mobile_number,email',
            'address',
            'flowerProduct:product_id,name',
            'order:id,order_id,request_id,rider_id',
            'order.rider:id,rider_id,rider_name',
        ];

        $excludeStatuses = ['expired', 'dead'];

        // Hide "pending, starts today, unpaid"
        $shouldHide = function ($sub) use ($today) {
            if (strtolower($sub->status ?? '') !== 'pending') return false;
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

        // 1) Tomorrow Delivery (active tomorrow)
        $activeTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'paused', 'pending'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $tomorrow->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tomorrow->toDateString())
            ->get()
            ->filter(function ($s) use ($tomorrow) {
                if ($s->pause_start_date && $s->pause_end_date) {
                    $ps = Carbon::parse($s->pause_start_date)->startOfDay();
                    $pe = Carbon::parse($s->pause_end_date)->endOfDay();
                    if ($ps->lte($tomorrow) && $pe->gte($tomorrow)) return false;
                }
                return true;
            })
            ->reject($shouldHide)
            ->values();

        // 2) Starting Tomorrow
        $startingTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->whereDate('start_date', '=', $tomorrow->toDateString())
            ->get()
            ->reject($shouldHide)
            ->values();

        // 3) Pausing from Tomorrow
        $pausingTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->whereDate('pause_start_date', '=', $tomorrow->toDateString())
            ->get()
            ->reject($shouldHide)
            ->values();

        // 4) Tomorrow Customize Orders
        $customizeTomorrow = FlowerRequest::with($withRequests)
            ->whereDate('date', '=', $tomorrow->toDateString())
            ->get();

        // 5) Pause → Active from Tomorrow (pause_end_date == today)
        $resumingTomorrow = Subscription::with($withSubs)
            ->whereNotIn('status', $excludeStatuses)
            ->whereNotNull('pause_end_date')
            ->whereDate('pause_end_date', '=', $today->toDateString())
            ->get()
            ->reject($shouldHide)
            ->values();

        // Map helpers
        $mapSub = function ($s) {
            $user    = $s->users;
            $order   = $s->order;
            $product = $s->flowerProducts;

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
            $order   = $fr->order;
            $rider   = $order?->rider;

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
                'request_id'     => $fr->request_id,
                'order_id'       => $order?->order_id,
                'status'         => $fr->status ?? '—',
                'date'           => $fr->date ? (string)$fr->date : null,
                'time'           => $fr->time ? (string)$fr->time : null,

                'customer'       => $user?->name ?? '—',
                'phone'          => $user?->mobile_number ?? null,
                'email'          => $user?->email ?? null,

                'product'        => $product?->name ?? '—',
                'address'        => $address ?: '—',
                'apartment_name' => $addr?->apartment_name ?? '',

                'rider_id'       => $rider?->rider_id ?? null,
                'rider_name'     => $rider?->rider_name ?? '—',
            ];
        };

        $data = [
            'today'              => $today->toDateString(),
            'tomorrow'           => $tomorrow->toDateString(),

            'activeTomorrow'     => $activeTomorrow->map($mapSub)->all(),
            'startingTomorrow'   => $startingTomorrow->map($mapSub)->all(),
            'pausingTomorrow'    => $pausingTomorrow->map($mapSub)->all(),
            'customizeTomorrow'  => $customizeTomorrow->map($mapRequest)->all(),
            'resumingTomorrow'   => $resumingTomorrow->map($mapSub)->all(),
        ];

        return view('admin.reports.tomorrow-subscriptions', $data);
    }
}
