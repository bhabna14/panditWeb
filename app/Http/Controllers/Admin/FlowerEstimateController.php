<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Subscription;
use App\Http\Controllers\Controller;

class FlowerEstimateController extends Controller
{
    public function index(Request $request)
    {
        // ---- Parse filters ---------------------------------------------------
        $preset = $request->string('preset')->toString();       // today|yesterday|this_month|last_month
        $mode   = $request->string('mode')->toString() ?: 'day';// day|month (UI toggle)

        [$start, $end] = $this->resolveRange($request, $preset);

        // Guard: ensure start <= end
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // ---- Build daily numbers (canonical source) -------------------------
        $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
        $dailyEstimates = [];

        foreach ($period as $day) {
            $subs = Subscription::with([
                'flowerProducts:id,product_id,name',
                'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
            ])
                ->activeOn($day)
                ->get();

            $byProduct = $subs->groupBy('product_id');

            $productsForDay = [];
            $grandTotalForDay = 0;

            foreach ($byProduct as $productId => $subsForProduct) {
                $product = optional($subsForProduct->first())->flowerProducts;
                $subsCount = $subsForProduct->count();

                $items = [];
                $productTotal = 0;

                if ($product) {
                    foreach ($product->packageItems as $pi) {
                        // Per-subscription quantity and price (as stored)
                        $perItemQty   = (float) ($pi->quantity ?? 0);
                        $origUnit     = trim(strtolower($pi->unit ?? ''));
                        $pricePerOrig = (float) ($pi->price ?? 0); // price per "origUnit"

                        // Total qty across active subs (in original unit first)
                        $totalQtyInOrigUnit = $perItemQty * $subsCount;

                        // Convert price to BASE unit to be consistent during unit folding
                        // and convert quantity to BASE as well.
                        $category = $this->inferCategory($origUnit); // weight|volume|count|unknown
                        $factorToBase = $this->toBaseFactor($origUnit); // e.g., kg -> 1000 g

                        // If unknown unit, treat it like "count" (no conversion)
                        if ($category === 'unknown') {
                            $category = 'count';
                            $origUnit = 'pcs';
                            $factorToBase = 1.0;
                        }

                        // Price per base unit (e.g., Rs/kg -> Rs/gram)
                        $pricePerBase = ($factorToBase > 0) ? $pricePerOrig / $factorToBase : $pricePerOrig;

                        // Total qty in base (e.g., grams / ml / pcs)
                        $totalQtyBase = $totalQtyInOrigUnit * $factorToBase;

                        // Format totals into a friendly unit (e.g., 2500 g -> 2.5 kg)
                        [$friendlyQty, $friendlyUnit, $displayFactorFromBase] = $this->formatFromBase($totalQtyBase, $category);

                        // Compute total price using base quantities (most stable)
                        $totalPrice = $pricePerBase * $totalQtyBase;

                        $items[] = [
                            'item_name'        => $pi->item_name,
                            'category'         => $category,
                            'per_item_qty'     => $perItemQty,
                            'per_item_unit'    => $origUnit,          // original unit (as stored)
                            'per_item_price'   => $pricePerOrig,      // price per original unit
                            'total_qty_base'   => $totalQtyBase,      // numeric in base
                            'total_qty_disp'   => $friendlyQty,       // numeric (formatted unit)
                            'total_unit_disp'  => $friendlyUnit,      // e.g., kg / g / L / ml / pcs
                            'total_price'      => $totalPrice,        // Rs
                        ];

                        $productTotal += $totalPrice;
                    }
                }

                $grandTotalForDay += $productTotal;

                $productsForDay[$productId] = [
                    'product'        => $product,
                    'subs_count'     => $subsCount,
                    'items'          => $items,
                    'product_total'  => $productTotal,
                ];
            }

            $dailyEstimates[$day->toDateString()] = [
                'products'            => $productsForDay,
                'grand_total_amount'  => $grandTotalForDay,
            ];
        }

        // ---- Month-wise rollup (aggregate from daily) -----------------------
        $monthlyEstimates = [];
        if ($mode === 'month') {
            // bucket by YYYY-MM
            foreach ($dailyEstimates as $dateStr => $payload) {
                $monthKey = Carbon::parse($dateStr)->format('Y-m');

                if (!isset($monthlyEstimates[$monthKey])) {
                    $monthlyEstimates[$monthKey] = [
                        'month_label' => Carbon::parse($dateStr)->format('M Y'),
                        'products'    => [], // product_id => items aggregated
                        'grand_total' => 0,
                    ];
                }

                foreach ($payload['products'] as $pid => $row) {
                    if (!isset($monthlyEstimates[$monthKey]['products'][$pid])) {
                        $monthlyEstimates[$monthKey]['products'][$pid] = [
                            'product'       => $row['product'],
                            'subs_days'     => 0, // how many subscription-day instances (not unique subs)
                            'items'         => [], // item_name + category key aggregation
                            'product_total' => 0,
                        ];
                    }

                    // add subscription day count (count of subs that day)
                    $monthlyEstimates[$monthKey]['products'][$pid]['subs_days'] += $row['subs_count'];

                    // aggregate items by (item_name, category) in BASE UNITS
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

            // After accumulation, compute friendly quantities for month view
            foreach ($monthlyEstimates as $mkey => &$mBlock) {
                foreach ($mBlock['products'] as &$pBlock) {
                    foreach ($pBlock['items'] as &$iBlock) {
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase(
                            $iBlock['total_qty_base'],
                            $iBlock['category']
                        );
                        $iBlock['total_qty_disp'] = $qtyDisp;
                        $iBlock['total_unit_disp'] = $unitDisp;
                    }
                }
            }
            unset($mBlock, $pBlock, $iBlock);
        }

        // ---- Render ----------------------------------------------------------
        return view('admin.reports.flower-estimates', [
            'start'            => $start->toDateString(),
            'end'              => $end->toDateString(),
            'mode'             => $mode,
            'preset'           => $preset,
            'dailyEstimates'   => $dailyEstimates,
            'monthlyEstimates' => $monthlyEstimates,
        ]);
    }

    // ----------------------------- Helpers -----------------------------------

    private function resolveRange(Request $request, ?string $preset): array
    {
        if ($preset) {
            $today = Carbon::today();
            switch ($preset) {
                case 'today':
                    return [$today->copy()->startOfDay(), $today->copy()->endOfDay()];
                case 'yesterday':
                    $y = $today->copy()->subDay();
                    return [$y->startOfDay(), $y->endOfDay()];
                case 'this_month':
                    $s = $today->copy()->startOfMonth();
                    $e = $today->copy()->endOfMonth();
                    return [$s, $e];
                case 'last_month':
                    $s = $today->copy()->subMonthNoOverflow()->startOfMonth();
                    $e = $today->copy()->subMonthNoOverflow()->endOfMonth();
                    return [$s, $e];
            }
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
            default => 1.0, // unknown treated as count
        };
    }

    private function formatFromBase(float $qtyBase, string $category): array
    {
        // Returns [friendlyQty, friendlyUnit, factorFromBase]
        if ($category === 'weight') {
            if ($qtyBase >= 1000) {
                return [round($qtyBase / 1000, 3), 'kg', 1/1000];
            }
            return [round($qtyBase, 3), 'g', 1.0];
        }
        if ($category === 'volume') {
            if ($qtyBase >= 1000) {
                return [round($qtyBase / 1000, 3), 'L', 1/1000];
            }
            return [round($qtyBase, 3), 'ml', 1.0];
        }
        // count
        return [round($qtyBase, 0), 'pcs', 1.0];
    }

    private function formatQtyByCategoryFromBase(float $qtyBase, string $category): array
    {
        [$q, $u] = match ($category) {
            'weight' => $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'kg'] : [round($qtyBase, 3), 'g'],
            'volume' => $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'L']  : [round($qtyBase, 3), 'ml'],
            default  => [round($qtyBase, 0), 'pcs'],
        };
        return [$q, $u];
    }
}
