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
        // ---- Filters ---------------------------------------------------------
        $preset = $request->string('preset')->toString();       // today|yesterday|this_month|last_month
        $mode   = $request->string('mode')->toString() ?: 'day';// day|month

        [$start, $end] = $this->resolveRange($request, $preset);
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // ---- Build daily numbers --------------------------------------------
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

            $productsForDay   = [];
            $grandTotalForDay = 0.0;

            foreach ($byProduct as $productId => $subsForProduct) {
                $product   = optional($subsForProduct->first())->flowerProducts;
                $subsCount = $subsForProduct->count();

                $items        = [];
                $productTotal = 0.0;

                if ($product) {
                    foreach ($product->packageItems as $pi) {
                        // Quantities: per-subscription qty stored in DB
                        $perItemQty = (float) ($pi->quantity ?? 0);
                        $origUnit   = strtolower(trim($pi->unit ?? ''));

                        // Prices: DB 'price' is per-subscription item price (NOT per unit)
                        $itemPricePerSub = (float) ($pi->price ?? 0);

                        // Convert quantity for totals (base units: g / ml / pcs)
                        $category     = $this->inferCategory($origUnit);
                        if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                        $toBaseFactor = $this->toBaseFactor($origUnit);

                        $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                        // 💡 Price totals use per-subscription item price
                        $totalPrice = $itemPricePerSub * $subsCount;

                        $items[] = [
                            'item_name'        => $pi->item_name,
                            'category'         => $category,
                            'per_item_qty'     => $perItemQty,
                            'per_item_unit'    => $origUnit,
                            'item_price_per_sub'=> $itemPricePerSub,  // shown as "Item Price (₹)"
                            'total_qty_base'   => $totalQtyBase,
                            'total_qty_disp'   => $qtyDisp,
                            'total_unit_disp'  => $unitDisp,
                            'total_price'      => $totalPrice,
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
                    // Also helpful to display bundle price per sub:
                    'bundle_total_per_sub' => array_sum(array_column($items, 'item_price_per_sub')),
                ];
            }

            $dailyEstimates[$day->toDateString()] = [
                'products'           => $productsForDay,
                'grand_total_amount' => $grandTotalForDay,
            ];
        }

        // ---- Month-wise rollup ----------------------------------------------
        $monthlyEstimates = [];
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
                            'subs_days'     => 0,      // sum of active subs per day (not unique subs)
                            'items'         => [],     // aggregated in base units + price totals
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

            // Display-friendly units after aggregation
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
        }

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
            return match ($preset) {
                'today'      => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
                'yesterday'  => [$today->copy()->subDay()->startOfDay(), $today->copy()->subDay()->endOfDay()],
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
