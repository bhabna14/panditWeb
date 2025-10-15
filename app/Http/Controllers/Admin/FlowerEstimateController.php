<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\FlowerRequest;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlowerEstimatesController extends Controller
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

        // ---- Tomorrow (strict effective rule) --------------------------------
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

        foreach ($period as $day) {
            // Use the same scope so daily calc matches tomorrow rule exactly
            $subs = Subscription::with([
                    'flowerProducts:id,product_id,name',
                    'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
                ])
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

                if ($product && $product->relationLoaded('packageItems')) {
                    foreach ($product->packageItems as $pi) {
                        $perItemQty      = (float) ($pi->quantity ?? 0);
                        $origUnit        = strtolower(trim((string)($pi->unit ?? '')));
                        $itemPricePerSub = (float) ($pi->price ?? 0);

                        $category     = $this->inferCategory($origUnit);
                        if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                        $toBaseFactor = $this->toBaseFactor($origUnit);

                        $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor; // base for category
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                        $totalPrice = $itemPricePerSub * $subsCount;

                        $items[] = [
                            'item_name'          => $pi->item_name,
                            'category'           => $category,
                            'per_item_qty'       => $perItemQty,
                            'per_item_unit'      => $origUnit,
                            'item_price_per_sub' => $itemPricePerSub,
                            'total_qty_base'     => $totalQtyBase,
                            'total_qty_disp'     => $qtyDisp,
                            'total_unit_disp'    => $unitDisp,
                            'total_price'        => $totalPrice,
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

        // ======= Lookups for the "Assign Vendor" modal (unchanged) =======
        $vendors = FlowerVendor::select('vendor_id', 'vendor_name')->orderBy('vendor_name')->get();
        $riders  = RiderDetails::select('rider_id', 'rider_name')->orderBy('rider_name')->get();
        $flowers = FlowerProduct::select('product_id', 'name')->orderBy('name')->get();
        $units   = PoojaUnit::select('id', 'unit_name')->orderBy('unit_name')->get();

        // name → product_id
        $flowerNameToId = $flowers->pluck('product_id', 'name')->toArray();

        // symbol → unit_id
        $normalizeUnit = fn (?string $raw) => $this->normalizeUnitSymbol($raw);
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $sym = $normalizeUnit($u->unit_name);
            if (!isset($unitSymbolToId[$sym])) $unitSymbolToId[$sym] = $u->id;
        }

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
     * Pull all subscriptions that are "effectively active" on a single date
     * using the model scope (handles pending/paused/resume/expired/etc).
     */
    protected function fetchActiveSubsEffectiveOn(Carbon $date)
    {
        return Subscription::with([
                'flowerProducts:id,product_id,name',
                'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
            ])
            ->activeOn($date)
            ->get();
    }

    /**
     * Convert a set of subs for a date into the same structure your Blade expects.
     */
    protected function buildEstimateForSubsOnDate($subs, Carbon $date): array
    {
        $byProduct = $subs->groupBy('product_id');
        $products  = [];
        $grand     = 0.0;

        foreach ($byProduct as $productId => $subsForProduct) {
            $product   = optional($subsForProduct->first())->flowerProducts;
            $subsCount = $subsForProduct->count();
            $items     = [];
            $productTotal = 0.0;

            if ($product && $product->relationLoaded('packageItems')) {
                foreach ($product->packageItems as $pi) {
                    $perItemQty      = (float) ($pi->quantity ?? 0);
                    $origUnit        = strtolower(trim((string)($pi->unit ?? '')));
                    $itemPricePerSub = (float) ($pi->price ?? 0);

                    $category     = $this->inferCategory($origUnit);
                    if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                    $toBaseFactor = $this->toBaseFactor($origUnit);

                    $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                    $totalPrice = $itemPricePerSub * $subsCount;

                    $items[] = [
                        'item_name'          => $pi->item_name,
                        'category'           => $category,
                        'per_item_qty'       => $perItemQty,
                        'per_item_unit'      => $origUnit,
                        'item_price_per_sub' => $itemPricePerSub,
                        'total_qty_base'     => $totalQtyBase,
                        'total_qty_disp'     => $qtyDisp,
                        'total_unit_disp'    => $unitDisp,
                        'total_price'        => $totalPrice,
                    ];

                    $productTotal += $totalPrice;
                }
            }

            $products[$productId] = [
                'product'              => $product,
                'subs_count'           => $subsCount,
                'items'                => $items,
                'product_total'        => $productTotal,
                'bundle_total_per_sub' => array_sum(array_column($items, 'item_price_per_sub')),
            ];
            $grand += $productTotal;
        }

        // Totals by item across all products for that date
        $totalsByItemBase = [];
        foreach ($products as $row) {
            foreach ($row['items'] as $it) {
                $key = strtolower($it['item_name']).'|'.$it['category'];
                if (!isset($totalsByItemBase[$key])) {
                    $totalsByItemBase[$key] = [
                        'item_name'      => $it['item_name'],
                        'category'       => $it['category'],
                        'total_qty_base' => 0.0,
                    ];
                }
                $totalsByItemBase[$key]['total_qty_base'] += $it['total_qty_base'];
            }
        }

        return [
            'products'             => $products,
            'grand_total_amount'   => $grand,
            'totals_by_item'       => $this->formatTotalsByItem($totalsByItemBase),
        ];
    }

    // ================== Helpers (units & formatting) ==================

    protected function inferCategory(string $u): string
    {
        $u = strtolower(trim($u));
        if (in_array($u, ['kg','kilogram','kilograms','kgs','g','gram','grams','gm','mg'])) return 'weight';
        if (in_array($u, ['l','lt','liter','litre','liters','litres','ml','milliliter','millilitre','milliliters','millilitres'])) return 'volume';
        if (in_array($u, ['pcs','pc','piece','pieces','count'])) return 'count';

        if (str_contains($u, 'kilo') || str_contains($u, 'gram')) return 'weight';
        if (str_contains($u, 'lit') || str_contains($u, 'millil')) return 'volume';
        if (str_contains($u, 'piece') || str_contains($u, 'pcs') || str_contains($u, 'count')) return 'count';

        return 'unknown';
    }

    protected function toBaseFactor(string $u): float
    {
        $u = strtolower(trim($u));
        // weight base: g
        if (in_array($u, ['kg','kilogram','kilograms','kgs'])) return 1000.0;
        if (in_array($u, ['g','gram','grams','gm'])) return 1.0;
        if ($u === 'mg') return 0.001;

        // volume base: ml
        if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return 1000.0;
        if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return 1.0;

        // count base: pcs
        if (in_array($u, ['pcs','pc','piece','pieces','count'])) return 1.0;

        // fallbacks
        if (str_contains($u, 'kilo')) return 1000.0;
        if (str_contains($u, 'gram')) return 1.0;
        if (str_contains($u, 'millil')) return 1.0;
        if (str_contains($u, 'lit')) return 1000.0;
        if (str_contains($u, 'piece') || str_contains($u, 'pcs') || str_contains($u, 'count')) return 1.0;

        return 1.0;
    }

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
        return [round($base, 3), 'pcs'];
    }

    protected function formatTotalsByItem(array $baseRows): array
    {
        $out = [];
        foreach ($baseRows as $row) {
            [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($row['total_qty_base'], $row['category']);
            $out[] = [
                'item_name'       => $row['item_name'],
                'total_qty_disp'  => $qtyDisp,
                'total_unit_disp' => $unitDisp,
            ];
        }
        usort($out, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));
        return $out;
    }

    protected function formatTotalsByCategory(array $catBase): array
    {
        $labels = [
            'weight' => 'Weight',
            'volume' => 'Volume',
            'count'  => 'Count',
        ];
        $out = [];
        foreach ($catBase as $cat => $base) {
            [$qty, $unit] = $this->formatQtyByCategoryFromBase($base, $cat);
            $out[] = [
                'label'           => $labels[$cat] ?? ucfirst($cat),
                'total_qty_disp'  => $qty,
                'total_unit_disp' => $unit,
            ];
        }
        return $out;
    }

    protected function normalizeUnitSymbol(?string $raw): string
    {
        $u = strtolower(trim((string)$raw));
        if (in_array($u, ['kg','kilogram','kilograms','kgs'])) return 'kg';
        if (in_array($u, ['g','gram','grams','gm'])) return 'g';
        if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return 'l';
        if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return 'ml';
        if (in_array($u, ['pcs','pc','piece','pieces','count'])) return 'pcs';
        if (str_contains($u, 'kilo')) return 'kg';
        if ($u === 'mg' || str_contains($u, 'gram')) return 'g';
        if (str_contains($u, 'millil')) return 'ml';
        if (str_contains($u, 'lit')) return 'l';
        if (str_contains($u, 'piece') || str_contains($u, 'pcs') || str_contains($u, 'count')) return 'pcs';
        return 'pcs';
    }

    // ======= Your existing resolveRange(...) goes here =======
    protected function resolveRange(Request $request, ?string $preset): array
    {
        // Keep your existing implementation for resolving $start / $end.
        // Stub (replace with your original):
        $start = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today();
        $end   = $request->filled('end_date')   ? Carbon::parse($request->input('end_date'))   : Carbon::today();

        if ($preset === 'today')     { $start = Carbon::today(); $end = Carbon::today(); }
        if ($preset === 'yesterday') { $start = Carbon::yesterday(); $end = Carbon::yesterday(); }
        if ($preset === 'tomorrow')  { $start = Carbon::tomorrow(); $end = Carbon::tomorrow(); }
        if ($preset === 'this_month'){ $start = Carbon::now()->startOfMonth(); $end = Carbon::now()->endOfMonth(); }
        if ($preset === 'last_month'){ $start = Carbon::now()->subMonthNoOverflow()->startOfMonth(); $end = Carbon::now()->subMonthNoOverflow()->endOfMonth(); }

        return [$start->startOfDay(), $end->endOfDay()];
    }
}
