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
    // ---- Filters ---------------------------------------------------------
    $preset = $request->string('preset')->toString();        // today|yesterday|tomorrow|this_month|last_month
    $mode   = $request->string('mode')->toString() ?: 'day'; // day|month

    [$start, $end] = $this->resolveRange($request, $preset);

    // If user switched to Month view but didn't send dates or a preset, default to whole current month
    if ($mode === 'month' && !$request->filled('start_date') && !$request->filled('end_date') && !$preset) {
        $today = Carbon::today();
        $start = $today->copy()->startOfMonth();
        $end   = $today->copy()->endOfMonth();
    }

    // Ensure start <= end
    if ($end->lt($start)) {
        [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
    }

    // ---- Tomorrow (separate, with effective end & pause handling) --------
    $tomorrow = Carbon::tomorrow()->startOfDay();
    $tomorrowSubs     = $this->fetchActiveSubsEffectiveOn($tomorrow);
    $tomorrowEstimate = $this->buildEstimateForSubsOnDate($tomorrowSubs, $tomorrow);

    // ---- Build daily numbers + RANGE GRAND TOTALS ------------------------
    $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
    $dailyEstimates = [];

    // overall range totals (base units)
    $rangeTotalsByItemBase = []; // key: "name|category" => total_qty_base
    $rangeTotalsByCategoryBase = [
        'weight' => 0.0, // grams
        'volume' => 0.0, // milliliters
        'count'  => 0.0, // pieces
    ];

        $excludeStats = ['expired', 'dead'];


    foreach ($period as $day) {
        $subs = Subscription::with([
                'flowerProducts:id,product_id,name',
                'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
            ])
                ->whereNotIn('status', $excludeStats)

            ->activeOn($day)
            ->get();

        $byProduct = $subs->groupBy('product_id');

        $productsForDay   = [];
        $grandTotalForDay = 0.0;

        // day-level totals by item (across all products)
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

                    $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor; // base for category
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

                    // --- aggregate to day totals (by item)
                    $key = strtolower($pi->item_name).'|'.$category;
                    if (!isset($dayTotalsByItemBase[$key])) {
                        $dayTotalsByItemBase[$key] = [
                            'item_name'      => $pi->item_name,
                            'category'       => $category,
                            'total_qty_base' => 0.0,
                        ];
                    }
                    $dayTotalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;

                    // --- aggregate to RANGE totals (by item)
                    if (!isset($rangeTotalsByItemBase[$key])) {
                        $rangeTotalsByItemBase[$key] = [
                            'item_name'      => $pi->item_name,
                            'category'       => $category,
                            'total_qty_base' => 0.0,
                        ];
                    }
                    $rangeTotalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;

                    // --- aggregate to RANGE totals (by category)
                    $rangeTotalsByCategoryBase[$category] += $totalQtyBase;
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

        // format day totals for display
        $dayTotalsForDisplay = $this->formatTotalsByItem($dayTotalsByItemBase);

        $dailyEstimates[$day->toDateString()] = [
            'products'           => $productsForDay,
            'grand_total_amount' => $grandTotalForDay,
            'totals_by_item'     => $dayTotalsForDisplay,
        ];
    }

    // ---- Month-wise rollup (shown when $mode === 'month') ----------------
    $monthlyEstimates = [];
    if ($mode === 'month') {
        foreach ($dailyEstimates as $dateStr => $payload) {
            $monthKey = Carbon::parse($dateStr)->format('Y-m');

            if (!isset($monthlyEstimates[$monthKey])) {
                $monthlyEstimates[$monthKey] = [
                    'month_label' => Carbon::parse($dateStr)->format('M Y'),
                    'products'    => [],
                    'grand_total' => 0.0,
                    'totals_by_item_base' => [],
                ];
            }

            foreach ($payload['products'] as $pid => $row) {
                if (!isset($monthlyEstimates[$monthKey]['products'][$pid])) {
                    $monthlyEstimates[$monthKey]['products'][$pid] = [
                        'product'       => $row['product'],
                        'subs_days'     => 0,
                        'items'         => [],
                        'product_total' => 0.0,
                    ];
                }

                $monthlyEstimates[$monthKey]['products'][$pid]['subs_days'] += $row['subs_count'];

                foreach ($row['items'] as $it) {
                    $key = strtolower($it['item_name']).'|'.$it['category'];

                    if (!isset($monthlyEstimates[$monthKey]['products'][$pid]['items'][$key])) {
                        $monthlyEstimates[$monthKey]['products'][$pid]['items'][$key] = [
                            'item_name'      => $it['item_name'],
                            'category'       => $it['category'],
                            'total_qty_base' => 0.0,
                            'total_price'    => 0.0,
                        ];
                    }

                    $monthlyEstimates[$monthKey]['products'][$pid]['items'][$key]['total_qty_base'] += $it['total_qty_base'];
                    $monthlyEstimates[$monthKey]['products'][$pid]['items'][$key]['total_price']    += $it['total_price'];

                    // month-level totals by item
                    if (!isset($monthlyEstimates[$monthKey]['totals_by_item_base'][$key])) {
                        $monthlyEstimates[$monthKey]['totals_by_item_base'][$key] = [
                            'item_name'      => $it['item_name'],
                            'category'       => $it['category'],
                            'total_qty_base' => 0.0,
                        ];
                    }
                    $monthlyEstimates[$monthKey]['totals_by_item_base'][$key]['total_qty_base'] += $it['total_qty_base'];
                }

                $monthlyEstimates[$monthKey]['products'][$pid]['product_total'] += $row['product_total'];
                $monthlyEstimates[$monthKey]['grand_total'] += $row['product_total'];
            }
        }

        // finalize formatting
        foreach ($monthlyEstimates as &$mBlock) {
            foreach ($mBlock['products'] as &$pBlock) {
                foreach ($pBlock['items'] as &$iBlock) {
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase(
                        $iBlock['total_qty_base'],
                        $iBlock['category']
                    );
                    $iBlock['total_qty_disp']  = $qtyDisp;
                    $iBlock['total_unit_disp'] = $unitDisp;
                }
            }

            // compute display array for month-level totals-by-item
            $mBlock['totals_by_item'] = $this->formatTotalsByItem($mBlock['totals_by_item_base']);
            unset($mBlock['totals_by_item_base']);
        }
        unset($mBlock, $pBlock, $iBlock);
    }

    // ---- RANGE GRAND TOTALS (display ready) ------------------------------
    $rangeTotals = [
        'by_item'     => $this->formatTotalsByItem($rangeTotalsByItemBase),
        'by_category' => $this->formatTotalsByCategory($rangeTotalsByCategoryBase),
    ];

    // ======= NEW: Lookups for the "Assign Vendor" modal =======
    // (No 'symbol' column needed anywhere.)
    $vendors = \App\Models\FlowerVendor::select('vendor_id', 'vendor_name')->orderBy('vendor_name')->get();
    $riders  = \App\Models\RiderDetails::select('rider_id', 'rider_name')->orderBy('rider_name')->get();
    $flowers = \App\Models\FlowerProduct::select('product_id', 'name')->orderBy('name')->get();
    $units   = \App\Models\PoojaUnit::select('id', 'unit_name')->orderBy('unit_name')->get();

    // name → product_id (exact match for auto prefill)
    $flowerNameToId = $flowers->pluck('product_id', 'name')->toArray();

    // Normalize unit_name -> base symbol we use in UI ('kg','g','l','ml','pcs')
    $normalizeUnit = function (?string $raw): string {
        $u = strtolower(trim((string)$raw));
        // exact common tokens
        if (in_array($u, ['kg','kilogram','kilograms','kgs'])) return 'kg';
        if (in_array($u, ['g','gram','grams','gm'])) return 'g';
        if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return 'l';
        if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return 'ml';
        if (in_array($u, ['pcs','pc','piece','pieces','count'])) return 'pcs';

        // fallback by substring (very forgiving)
        if (str_contains($u, 'kilo')) return 'kg';
        if ($u === 'mg' || str_contains($u, 'gram')) return 'g';
        if (str_contains($u, 'millil')) return 'ml';
        if (str_contains($u, 'lit')) return 'l';
        if (str_contains($u, 'piece') || str_contains($u, 'pcs') || str_contains($u, 'count')) return 'pcs';

        // default to pcs
        return 'pcs';
    };

    // symbol → unit_id (first match wins)
    $unitSymbolToId = [];
    foreach ($units as $u) {
        $sym = $normalizeUnit($u->unit_name);
        if (!isset($unitSymbolToId[$sym])) {
            $unitSymbolToId[$sym] = $u->id;
        }
    }

    // ======= Return view with NEW data =======
    return view('admin.reports.flower-estimates', [
        'start'              => $start->toDateString(),
        'end'                => $end->toDateString(),
        'mode'               => $mode,
        'preset'             => $preset,
        'dailyEstimates'     => $dailyEstimates,
        'monthlyEstimates'   => $monthlyEstimates,
        // Tomorrow block
        'tomorrowDate'       => $tomorrow->toDateString(),
        'tomorrowEstimate'   => $tomorrowEstimate,
        // Range grand totals
        'rangeTotals'        => $rangeTotals,

        // For modal
        'vendors'            => $vendors,
        'riders'             => $riders,
        'flowers'            => $flowers,
        'units'              => $units,
        'flowerNameToId'     => $flowerNameToId,
        'unitSymbolToId'     => $unitSymbolToId,
    ]);
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
