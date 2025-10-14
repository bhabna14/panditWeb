<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Subscription;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
class FlowerEstimateController extends Controller
{
 public function index(Request $request)
{

    $today    = Carbon::today();
    $tomorrow = Carbon::tomorrow()->startOfDay();

    // Relations for display (include rider on the order + latest paid payment)
    $with = [
        'users',                    // has mobile_number, email, custom PK `userid`
        'users.addressDetails',     // default address (UserAddress)
        'order',                    // shipping_* fields (if present)
        'order.rider:id,rider_id,rider_name',
        'flowerProducts:product_id,name',
        'latestPaidPayment',        // <-- used to decide if "pending today" is truly unpaid
        'flowerPayments'            // <-- optional fallback check for 'status'='paid'
    ];

    // We will consistently exclude these terminal statuses everywhere
    $excludeStatuses = ['expired', 'dead'];

    // Helper: skip "pending & starts today & unpaid"
    $shouldHide = function ($sub) use ($today) {
        if (strtolower($sub->status ?? '') !== 'pending') {
            return false;
        }
        // start date is today?
        $startsToday = $sub->start_date ? Carbon::parse($sub->start_date)->isSameDay($today) : false;
        if (!$startsToday) {
            return false;
        }

        // Paid check #1 (preferred): has a latestPaidPayment (payment_status = 'paid')
        $hasPaid = !empty($sub->latestPaidPayment);

        // Paid check #2 (fallback): any payment row with status='paid' (if your schema uses "status")
        if (!$hasPaid && $sub->relationLoaded('flowerPayments')) {
            $hasPaid = $sub->flowerPayments->contains(function ($p) {
                $ps = strtolower((string)($p->payment_status ?? ''));
                $s  = strtolower((string)($p->status ?? ''));
                return $ps === 'paid' || $s === 'paid';
            });
        }

        // Hide only if still unpaid
        return !$hasPaid;
    };

    // 1) Active tomorrow (your “estimate” rule + exclude paused that day)
    $activeTomorrow = Subscription::with($with)
        ->whereNotIn('status', $excludeStatuses)
        ->where(function ($q) {
            $q->whereIn('status', ['active', 'paused', 'pending'])
              ->orWhere('is_active', 1);
        })
        ->whereDate('start_date', '<=', $tomorrow->toDateString())
        ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tomorrow->toDateString())
        ->get()
        // paused on tomorrow? remove
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
        // NEW: hide pending that start today and are unpaid
        ->reject($shouldHide)
        ->values();

    // 2) Start tomorrow
    $startingTomorrow = Subscription::with($with)
        ->whereNotIn('status', $excludeStatuses)
        ->whereDate('start_date', '=', $tomorrow->toDateString())
        ->get()
        // (Rule targets "start date = today", so not necessary here, but safe if you want uniformity)
        ->reject($shouldHide)
        ->values();

    // 3) Pause starts tomorrow
    $pausingTomorrow = Subscription::with($with)
        ->whereNotIn('status', $excludeStatuses)
        ->whereDate('pause_start_date', '=', $tomorrow->toDateString())
        ->get()
        ->reject($shouldHide)
        ->values();

    // 4) End today (coalesced)
    $endingToday = Subscription::with($with)
        ->whereNotIn('status', $excludeStatuses)
        ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $today->toDateString())
        ->get()
        ->reject($shouldHide)
        ->values();

    // 5) End tomorrow (coalesced)
    $endingTomorrow = Subscription::with($with)
        ->whereNotIn('status', $excludeStatuses)
        ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '=', $tomorrow->toDateString())
        ->get()
        ->reject($shouldHide)
        ->values();

    // Normalize each row for the view
    $mapRow = function ($s) {
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

            // rider fields
            'rider_id'        => $rider?->rider_id ?? null,
            'rider_name'      => $riderName,
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


    /**
     * Query subscriptions active on a specific date using:
     * - start_date <= date
     * - COALESCE(new_date, end_date) >= date
     * - status in ['active','paused'] or is_active = 1
     * Then filter out those paused on the date.
     */
    private function fetchActiveSubsEffectiveOn(Carbon $date)
    {
        $subs = Subscription::with([
                'flowerProducts:id,product_id,name',
                'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
            ])
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'paused'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $date->toDateString())
            ->get();

        // Exclude paused on this date
        $filtered = $subs->filter(function ($s) use ($date) {
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                       && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
                if ($paused) return false;
            }
            return true;
        });

        return $filtered->values();
    }

    /**
     * Build the same structure you show for a "day", from a subscription collection.
     * + totals_by_item for that date (used for Tomorrow card).
     */
    private function buildEstimateForSubsOnDate($subs, Carbon $date): array
    {
        $byProduct = $subs->groupBy('product_id');

        $productsForDay   = [];
        $grandTotalForDay = 0.0;

        $dayTotalsByItemBase = [];

        foreach ($byProduct as $productId => $subsForProduct) {
            $product   = optional($subsForProduct->first())->flowerProducts;
            $subsCount = $subsForProduct->count();

            $items        = [];
            $productTotal = 0.0;

            if ($product) {
                foreach ($product->packageItems as $pi) {
                    $perItemQty      = (float) ($pi->quantity ?? 0);
                    $origUnit        = strtolower(trim($pi->unit ?? ''));
                    $itemPricePerSub = (float) ($pi->price ?? 0);

                    $category     = $this->inferCategory($origUnit);
                    if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                    $toBaseFactor = $this->toBaseFactor($origUnit);

                    $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                    $totalPrice = $itemPricePerSub * $subsCount;

                    $items[] = [
                        'item_name'         => $pi->item_name,
                        'category'          => $category,
                        'per_item_qty'      => $perItemQty,
                        'per_item_unit'     => $origUnit,
                        'item_price_per_sub'=> $itemPricePerSub,
                        'total_qty_base'    => $totalQtyBase,
                        'total_qty_disp'    => $qtyDisp,
                        'total_unit_disp'   => $unitDisp,
                        'total_price'       => $totalPrice,
                    ];

                    $productTotal += $totalPrice;

                    // aggregate for tomorrow totals-by-item
                    $key = strtolower($pi->item_name).'|'.$category;
                    if (!isset($dayTotalsByItemBase[$key])) {
                        $dayTotalsByItemBase[$key] = [
                            'item_name'      => $pi->item_name,
                            'category'       => $category,
                            'total_qty_base' => 0.0,
                        ];
                    }
                    $dayTotalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;
                }
            }

            $grandTotalForDay += $productTotal;

            $productsForDay[$productId] = [
                'product'              => $product,
                'subs_count'           => $subsCount,
                'items'                => $items,
                'product_total'        => $productTotal,
                'bundle_total_per_sub' => array_sum(array_column($items, 'item_price_per_sub')),
            ];
        }

        return [
            'date'               => $date->toDateString(),
            'products'           => $productsForDay,
            'grand_total_amount' => $grandTotalForDay,
            'totals_by_item'     => $this->formatTotalsByItem($dayTotalsByItemBase),
        ];
    }

    // --------------------- Helpers -------------------------------------------

    private function resolveRange(Request $request, ?string $preset): array
    {
        if ($preset) {
            $today = Carbon::today();
            return match ($preset) {
                'today'      => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
                'yesterday'  => [$today->copy()->subDay()->startOfDay(), $today->copy()->subDay()->endOfDay()],
                'tomorrow'   => [$today->copy()->addDay()->startOfDay(), $today->copy()->addDay()->endOfDay()],
                'this_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
                'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
                default      => $this->resolveRange($request, null),
            };
        }

        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        return [$start, $end];
    }

    private function inferCategory(string $unit): string
    {
        $u = strtolower(trim($unit));
        if (in_array($u, ['g', 'gm', 'gram', 'grams'])) return 'weight';
        if (in_array($u, ['kg', 'kgs', 'kilogram', 'kilograms'])) return 'weight';
        if (in_array($u, ['ml', 'milliliter', 'milliliters'])) return 'volume';
        if (in_array($u, ['l', 'lt', 'liter', 'litre', 'liters', 'litres'])) return 'volume';
        if (in_array($u, ['piece', 'pieces', 'pc', 'pcs', 'count'])) return 'count';
        return 'unknown';
    }

    private function toBaseFactor(string $unit): float
    {
        $u = strtolower(trim($unit));
        // Base units: g, ml, pcs
        return match ($u) {
            'g', 'gm', 'gram', 'grams' => 1.0,
            'kg', 'kgs', 'kilogram', 'kilograms' => 1000.0,
            'ml', 'milliliter', 'milliliters' => 1.0,
            'l', 'lt', 'liter', 'litre', 'liters', 'litres' => 1000.0,
            'piece', 'pieces', 'pc', 'pcs', 'count' => 1.0,
            default => 1.0,
        };
    }

    private function formatQtyByCategoryFromBase(float $qtyBase, string $category): array
    {
        return match ($category) {
            'weight' => $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'kg'] : [round($qtyBase, 3), 'g'],
            'volume' => $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'L']  : [round($qtyBase, 3), 'ml'],
            default  => [round($qtyBase, 0), 'pcs'],
        };
    }

    /** Convert aggregated base qty map (item-wise) into display rows */
    private function formatTotalsByItem(array $baseMap): array
    {
        $rows = [];
        foreach ($baseMap as $key => $info) {
            [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($info['total_qty_base'], $info['category']);
            $rows[$key] = [
                'item_name'       => $info['item_name'],
                'category'        => $info['category'],
                'total_qty_base'  => $info['total_qty_base'],
                'total_qty_disp'  => $qtyDisp,
                'total_unit_disp' => $unitDisp,
            ];
        }
        uasort($rows, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));
        return $rows;
    }

    /** Convert aggregated base qty (category-wise) into display rows */
    private function formatTotalsByCategory(array $baseByCat): array
    {
        // keys: weight(g), volume(ml), count(pcs)
        $out = [];
        foreach (['weight','volume','count'] as $cat) {
            [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($baseByCat[$cat] ?? 0, $cat);
            $label = match ($cat) {
                'weight' => 'Weight',
                'volume' => 'Volume',
                default  => 'Count',
            };
            $out[] = [
                'label'          => $label,
                'category'       => $cat,
                'total_qty_base' => (float) ($baseByCat[$cat] ?? 0),
                'total_qty_disp' => $qtyDisp,
                'total_unit_disp'=> $unitDisp,
            ];
        }
        return $out;
    }
}
