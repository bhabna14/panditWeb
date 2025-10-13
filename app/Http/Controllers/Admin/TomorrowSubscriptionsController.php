<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TomorrowSubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow()->startOfDay();

        // Eager load relations (no column-restrict to avoid custom PK pitfalls)
        $with = [
            'users',                     // has mobile_number, email, custom PK `userid`
            'users.addressDetails',      // default address (UserAddress)
            'order',                     // shipping_* fields (if present)
            'flowerProducts:product_id,name',
        ];

        // 1) Active tomorrow
        $activeTomorrow = Subscription::with($with)
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
                    if ($ps->lte($tomorrow) && $pe->gte($tomorrow)) {
                        return false; // paused on that day
                    }
                }
                return true;
            })->values();

        // 2) Start tomorrow
        $startingTomorrow = Subscription::with($with)
            ->whereDate('start_date', '=', $tomorrow->toDateString())
            ->get();

        // 3) Pause starts tomorrow
        $pausingTomorrow = Subscription::with($with)
            ->whereDate('pause_start_date', '=', $tomorrow->toDateString())
            ->get();

        // 4) End today (coalesced)
        $endingToday = Subscription::with($with)
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $today->toDateString())
            ->get();

        // 5) End tomorrow (coalesced)
        $endingTomorrow = Subscription::with($with)
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $tomorrow->toDateString())
            ->get();

        // Normalize each row for the view
        $mapRow = function ($s) {
            $user    = $s->users;
            $order   = $s->order;
            $product = $s->flowerProducts;

            // Address priority: order shipping_* -> user addressDetails
            $address = '';

            if ($order) {
                $parts = [];
                foreach ([
                    'shipping_name','shipping_address','shipping_street','shipping_area',
                    'shipping_city','shipping_state','shipping_pincode','shipping_zip',
                ] as $f) {
                    if (isset($order->$f) && filled($order->$f)) {
                        $parts[] = $order->$f;
                    }
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

            $aptName = ($user && $user->addressDetails) ? ($user->addressDetails->apartment_name ?? '') : '';


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
                'phone'           => $user?->mobile_number ?? null, // <-- fixed
                'email'           => $user?->email ?? null,
                'product'         => $product?->name ?? '—',
                'address'         => $address ?: '—',

                'address'         => $address ?: '—',
                'apartment_name'  => $aptName,   // <-- add this
            ];
        };

        $data = [
            'today'            => $today->toDateString(),
            'tomorrow'         => $tomorrow->toDateString(),
            'activeTomorrow'   => $activeTomorrow->map($mapRow)->all(),
            'startingTomorrow' => $startingTomorrow->map($mapRow)->all(),
            'pausingTomorrow'  => $pausingTomorrow->map($mapRow)->all(),
            'endingToday'      => $endingToday->map($mapRow)->all(),
            'endingTomorrow'   => $endingTomorrow->map($mapRow)->all(),
        ];

        return view('admin.reports.tomorrow-subscriptions', $data);
    }
}
