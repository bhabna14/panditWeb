<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Subscription;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FlowerEstimateController extends Controller
{
    public function index(Request $request)
    {
        // ---- Filters ---------------------------------------------------------
        $preset = $request->string('preset')->toString();        // today|yesterday|tomorrow|this_month|last_month
        $mode   = $request->string('mode')->toString() ?: 'day'; // day|month

        [$start, $end] = $this->resolveRange($request, $preset);
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // ---- Tomorrow (separate, with effective end & pause handling) --------
        $tomorrow = Carbon::tomorrow()->startOfDay();
        $tomorrowSubs = $this->fetchActiveSubsEffectiveOn($tomorrow);
        $tomorrowEstimate = $this->buildEstimateForSubsOnDate($tomorrowSubs, $tomorrow);

        // 👉 NEW: aggregate totals across all products for tomorrow
        $tomorrowTotals = $this->aggregateTotalsAcrossProducts($tomorrowEstimate['products'] ?? []);

        // ---- Build daily numbers (existing view) -----------------------------
        $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
        $dailyEstimates   = [];
        $dailyItemTotals  = []; // 👉 NEW: per-day totals (all products combined)

        foreach ($period as $day) {
            // Using your existing scope for the daily list (kept as-is).
            $subs = Subscription::with([
                    'flowerProducts:id,product_id,name',
                    'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
                ])
                ->activeOn($day) // uses start_date..end_date and pause exclusion
                ->get();

            $byProduct = $subs->groupBy('product_id');

            $productsForDay   = [];
            $grandTotalForDay = 0.0;

            foreach ($byProduct as $productId => $subsForProduct) {
                $product   = optional($subsForProduct->first())->flowerProducts;
                $subsCount = $subsForProduct->count();

                $items        = [];
                $productTotal = 0.0;

                if ($product) {
                    foreach ($product->packageItems as $e) {
                        $perItemQty      = (float) ($e->quantity ?? 0);
                        $origUnit        = strtolower(trim($e->unit ?? ''));
                        $itemPricePerSub = (float) ($e->price ?? 0);

                        $category     = $this->inferCategory($origUnit);
                        if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                        $toBaseFactor = $this->toBaseFactor($origUnit);

                        $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                        $totalPrice = $itemPricePerSub * $subsCount;

                        $items[] = [
                            'item_name'         => $e->item_name,
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

            $dayKey = $day->toDateString();

            $dailyEstimates[$dayKey] = [
                'products'           => $productsForDay,
                'grand_total_amount' => $grandTotalForDay,
            ];

            // 👉 NEW: compute totals across ALL products for this day
            $dailyItemTotals[$dayKey] = $this->aggregateTotalsAcrossProducts($productsForDay);
        }

        // ---- Month-wise rollup (existing view) -------------------------------
        $monthlyEstimates = [];
        $monthlyItemTotals = []; // 👉 NEW: per-month totals (all products combined)

        if ($mode === 'month') {
            foreach ($dailyEstimates as $dateStr => $payload) {
                $monthKey = Carbon::parse($dateStr)->format('Y-m');

                if (!isset($monthlyEstimates[$monthKey])) {
                    $monthlyEstimates[$monthKey] = [
                        'month_label' => Carbon::parse($dateStr)->format('M Y'),
                        'products'    => [],
                        'grand_total' => 0.0,
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
                    }

                    $monthlyEstimates[$monthKey]['products'][$pid]['product_total'] += $row['product_total'];
                    $monthlyEstimates[$monthKey]['grand_total'] += $row['product_total'];
                }
            }

            // Finalize item display units for product-level month items
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
            }
            unset($mBlock, $pBlock, $iBlock);

            // 👉 NEW: build overall per-month totals across ALL products
            foreach ($monthlyEstimates as $mKey => $mBlock) {
                $monthlyItemTotals[$mKey] = $this->aggregateTotalsAcrossProducts($mBlock['products']);
            }
        }

        return view('admin.reports.flower-estimates', [
            'start'              => $start->toDateString(),
            'end'                => $end->toDateString(),
            'mode'               => $mode,
            'preset'             => $preset,
            'dailyEstimates'     => $dailyEstimates,
            'monthlyEstimates'   => $monthlyEstimates,
            // 👉 NEW: pass our cross-product item totals
            'dailyItemTotals'    => $dailyItemTotals,
            'monthlyItemTotals'  => $monthlyItemTotals,
            // Tomorrow block
            'tomorrowDate'       => $tomorrow->toDateString(),
            'tomorrowEstimate'   => $tomorrowEstimate,
            'tomorrowTotals'     => $tomorrowTotals, // 👉 NEW
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
     */
    private function buildEstimateForSubsOnDate($subs, Carbon $date): array
    {
        $byProduct = $subs->groupBy('product_id');

        $productsForDay   = [];
        $grandTotalForDay = 0.0;

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
        ];
    }

    // --------------------- Helpers ----------------------------

    /**
     * 👉 NEW:
     * Aggregate totals (qty & price) across ALL products for a given payload structure.
     * Expects $products like [productId => ['items' => [...]]]
     * Returns a flat array keyed by "name|category" with qty in base + display units.
     */
    private function aggregateTotalsAcrossProducts(array $products): array
    {
        $totals = [];
        foreach ($products as $row) {
            foreach ($row['items'] ?? [] as $it) {
                $key = strtolower($it['item_name']).'|'.$it['category'];
                if (!isset($totals[$key])) {
                    $totals[$key] = [
                        'item_name'      => $it['item_name'],
                        'category'       => $it['category'],
                        'total_qty_base' => 0.0,
                        'total_price'    => 0.0,
                    ];
                }
                $totals[$key]['total_qty_base'] += (float)$it['total_qty_base'];
                $totals[$key]['total_price']    += (float)$it['total_price'];
            }
        }

        // compute display units
        foreach ($totals as &$t) {
            [$q, $u] = $this->formatQtyByCategoryFromBase($t['total_qty_base'], $t['category']);
            $t['total_qty_disp']  = $q;
            $t['total_unit_disp'] = $u;
        }
        unset($t);

        // sort alpha by name for stable UI (optional)
        uasort($totals, function ($a, $b) {
            return strcasecmp($a['item_name'], $b['item_name']);
        });

        return $totals;
    }

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
}
