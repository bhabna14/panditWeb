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

        // Base eager loads so we can show name/product/address nicely
        $with = [
            'users:userid,name,phone,email,address',
            'order',                          // assumes you have shipping_* or address fields here
            'flowerProducts:product_id,name', // product name
        ];

        // 1) Active tomorrow (uses same logic as your estimate: start <= tmr, coalesce(new_date,end_date) >= tmr, exclude paused range on tmr)
        $activeTomorrow = Subscription::with($with)
            ->where(function ($q) { // consider active/paused/is_active=1
                $q->whereIn('status', ['active', 'paused', 'pending'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $tomorrow->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tomorrow->toDateString())
            ->get()
            ->filter(function ($s) use ($tomorrow) {
                // Exclude if paused tomorrow (pause_start_date..pause_end_date overlaps tomorrow)
                if ($s->pause_start_date && $s->pause_end_date) {
                    $ps = Carbon::parse($s->pause_start_date)->startOfDay();
                    $pe = Carbon::parse($s->pause_end_date)->endOfDay();
                    if ($ps->lte($tomorrow) && $pe->gte($tomorrow)) {
                        return false;
                    }
                }
                return true;
            })->values();

        // 2) Subscriptions that START tomorrow
        $startingTomorrow = Subscription::with($with)
            ->whereDate('start_date', '=', $tomorrow->toDateString())
            ->get();

        // 3) Subscriptions that PAUSE starting tomorrow
        $pausingTomorrow = Subscription::with($with)
            ->whereDate('pause_start_date', '=', $tomorrow->toDateString())
            ->get();

        // 4) Subscriptions with (coalesced) END = today
        $endingToday = Subscription::with($with)
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $today->toDateString())
            ->get();

        // 5) Subscriptions with (coalesced) END = tomorrow
        $endingTomorrow = Subscription::with($with)
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $tomorrow->toDateString())
            ->get();

        // Build display rows (normalize address & convenient fields)
        $mapRow = function ($s) {
            $user   = $s->users;
            $order  = $s->order;
            $product= $s->flowerProducts;

            // Try order shipping fields first (rename if yours differ)
            $parts = [];
            foreach (['shipping_name','shipping_address','shipping_street','shipping_area','shipping_city','shipping_state','shipping_pincode','shipping_zip'] as $f) {
                if (isset($order->$f) && filled($order->$f)) $parts[] = $order->$f;
            }
            $address = trim(implode(', ', array_unique(array_filter($parts))));

            // Fallback to user->address or compose from common user fields if you have them
            if (!$address) {
                if ($user && filled($user->address)) {
                    $address = $user->address;
                } else {
                    $chunks = [];
                    foreach (['address_line1','address_line2','city','state','pincode','zip'] as $f) {
                        if ($user && isset($user->$f) && filled($user->$f)) $chunks[] = $user->$f;
                    }
                    $address = trim(implode(', ', $chunks));
                }
            }

            return [
                'subscription_id' => $s->subscription_id,
                'order_id'        => $s->order_id,
                'status'          => $s->status,
                'is_active'       => (int)$s->is_active,
                'start_date'      => optional($s->start_date) ? (string)$s->start_date : null,
                'end_date'        => optional($s->end_date) ? (string)$s->end_date : null,
                'new_date'        => optional($s->new_date) ? (string)$s->new_date : null,
                'pause_start'     => optional($s->pause_start_date) ? (string)$s->pause_start_date : null,
                'pause_end'       => optional($s->pause_end_date) ? (string)$s->pause_end_date : null,
                'customer'        => $user?->name ?? '—',
                'phone'           => $user?->phone ?? null,
                'email'           => $user?->email ?? null,
                'product'         => $product?->name ?? '—',
                'address'         => $address ?: '—',
            ];
        };

        $data = [
            'today'           => $today->toDateString(),
            'tomorrow'        => $tomorrow->toDateString(),
            'activeTomorrow'  => $activeTomorrow->map($mapRow)->all(),
            'startingTomorrow'=> $startingTomorrow->map($mapRow)->all(),
            'pausingTomorrow' => $pausingTomorrow->map($mapRow)->all(),
            'endingToday'     => $endingToday->map($mapRow)->all(),
            'endingTomorrow'  => $endingTomorrow->map($mapRow)->all(),
        ];

        return view('admin.reports.tomorrow-subscriptions', $data);
    }
}
