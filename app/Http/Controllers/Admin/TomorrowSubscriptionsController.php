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
            // include package items so we can build the totals-by-item block
            'flowerProducts:product_id,name',
            'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
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
            'flowerRequestItems', // <--- NEW
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
                    $ps = \Carbon\Carbon::parse($s->pause_start_date)->startOfDay();
                    $pe = \Carbon\Carbon::parse($s->pause_end_date)->endOfDay();
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

        // ------- NEW: Build "Tomorrow — Totals by Item (All Products)" -------
        // We compute ONLY from subscriptions that will actually deliver tomorrow (activeTomorrow).
        $tTotals = $this->computeTomorrowTotalsByItem($activeTomorrow);

        // Normalizers
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

            // Items normalize (safe array for modal JSON)
            $items = $fr->flowerRequestItems
                ? $fr->flowerRequestItems->map(function ($it) {
                    return [
                        'type'              => (string)($it->type ?? ''),
                        'garland_name'      => (string)($it->garland_name ?? ''),
                        'garland_quantity'  => (string)($it->garland_quantity ?? ''),
                        'garland_size'      => (string)($it->garland_size ?? ''),
                        'flower_name'       => (string)($it->flower_name ?? ''),
                        'flower_unit'       => (string)($it->flower_unit ?? ''),
                        'flower_quantity'   => (string)($it->flower_quantity ?? ''),
                        'flower_count'      => (string)($it->flower_count ?? ''),
                        'size'              => (string)($it->size ?? ''),
                    ];
                })->values()->all()
                : [];

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

                'items'          => $items, // <--- for modal
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

            // NEW for totals card
            'tTotals'            => $tTotals,
        ];

        return view('admin.reports.tomorrow-subscriptions', $data);
    }

    // ---------------------------------------------------------------------
    // Helpers for totals by item (kept local to this controller)
    // ---------------------------------------------------------------------

    /**
     * Build totals-by-item for tomorrow deliveries.
     * @param \Illuminate\Support\Collection $activeTomorrow  // collection of Subscription models (with flowerProducts.packageItems)
     * @return array [ ['item_name'=>..., 'total_qty_disp'=>..., 'total_unit_disp'=>...], ... ]
     */
    protected function computeTomorrowTotalsByItem($activeTomorrow): array
    {
        if ($activeTomorrow->isEmpty()) return [];

        // Count subs per product
        $subsCountByProduct = $activeTomorrow->groupBy('product_id')->map->count();

        $totalsByItemBase = []; // key: "name|category" => ['item_name'=>..., 'category'=>..., 'total_qty_base'=>float]

        foreach ($subsCountByProduct as $productId => $subsCount) {
            $product = optional($activeTomorrow->firstWhere('product_id', $productId))->flowerProducts;
            if (!$product || !$product->relationLoaded('packageItems')) continue;

            foreach ($product->packageItems as $pi) {
                $perItemQty = (float) ($pi->quantity ?? 0);
                $unitRaw    = strtolower(trim((string)($pi->unit ?? '')));
                [$category, $toBaseFactor] = $this->resolveCategoryAndFactor($unitRaw);

                $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;

                $key = strtolower($pi->item_name) . '|' . $category;
                if (!isset($totalsByItemBase[$key])) {
                    $totalsByItemBase[$key] = [
                        'item_name'      => $pi->item_name,
                        'category'       => $category,
                        'total_qty_base' => 0.0,
                    ];
                }
                $totalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;
            }
        }

        // Format for display
        $out = [];
        foreach ($totalsByItemBase as $row) {
            [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($row['total_qty_base'], $row['category']);
            $out[] = [
                'item_name'       => $row['item_name'],
                'total_qty_disp'  => $qtyDisp,
                'total_unit_disp' => $unitDisp,
            ];
        }

        // Sort by item name (optional, nice touch)
        usort($out, function ($a, $b) {
            return strcasecmp($a['item_name'], $b['item_name']);
        });

        return $out;
    }

    /**
     * Map a raw unit to (category, toBaseFactor).
     * Base units: weight->g, volume->ml, count->pcs.
     */
    protected function resolveCategoryAndFactor(string $u): array
    {
        // weight
        if (in_array($u, ['kg','kilogram','kilograms','kgs'])) return ['weight', 1000.0];
        if (in_array($u, ['g','gram','grams','gm']))           return ['weight', 1.0];

        // volume
        if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return ['volume', 1000.0];
        if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return ['volume', 1.0];

        // count / piecey fallbacks
        if (in_array($u, ['pcs','pc','piece','pieces','count'])) return ['count', 1.0];

        // forgiving substring fallbacks
        if (str_contains($u, 'kilo')) return ['weight', 1000.0];
        if ($u === 'mg' || str_contains($u, 'gram')) return ['weight', 1.0];
        if (str_contains($u, 'millil')) return ['volume', 1.0];
        if (str_contains($u, 'lit')) return ['volume', 1000.0];
        if (str_contains($u, 'piece') || str_contains($u, 'pcs') || str_contains($u, 'count')) return ['count', 1.0];

        // default to count
        return ['count', 1.0];
    }

    /**
     * Convert base qty to a display qty/unit per category.
     * weight: g->kg when >= 1000
     * volume: ml->l when >= 1000
     * count: pcs
     */
    protected function formatQtyByCategoryFromBase(float $base, string $category): array
    {
        if ($category === 'weight') {
            if ($base >= 1000) return [round($base / 1000, 3), 'kg'];
            return [round($base, 3), 'g'];
        }
        if ($category === 'volume') {
            if ($base >= 1000) return [round($base / 1000, 3), 'l'];
            return [round($base, 3), 'ml'];
        }
        // count
        return [round($base, 3), 'pcs'];
    }
}
