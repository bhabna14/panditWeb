<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use App\Models\FlowerDetails; // ← NEW
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use App\Models\FlowerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as DBFacade;

class FlowerEstimateController extends Controller
{

    public function index(Request $request)
    {
        // ---- Filters ---------------------------------------------------------
        $preset = $request->string('preset')->toString();        // today|yesterday|tomorrow|this_month|last_month
        $mode   = $request->string('mode')->toString() ?: 'day'; // day|month

        [$start, $end] = $this->resolveRange($request, $preset);

        if ($mode === 'month' && !$request->filled('start_date') && !$request->filled('end_date') && !$preset) {
            $today = Carbon::today();
            $start = $today->copy()->startOfMonth();
            $end   = $today->copy()->endOfMonth();
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // ---- FlowerDetails live price index (name → {unit, price}) ----------
        $fdIndex = FlowerDetails::query()
            ->select(['name', 'unit', 'price'])
            ->where('status', 'active')
            ->get()
            ->keyBy(function ($fd) {
                return strtolower(trim((string) $fd->name));
            });

        // ---- Tomorrow (with effective end & pause handling + FLOWER REQUESTS) -------
        $tomorrow = Carbon::tomorrow()->startOfDay();

        // subscriptions estimate (existing behavior)
        $tomorrowSubs     = $this->fetchActiveSubsEffectiveOn($tomorrow);
        $tomorrowEstimate = $this->buildEstimateForSubsOnDate($tomorrowSubs, $tomorrow, $fdIndex);

        // merge ad-hoc Flower Requests scheduled for tomorrow
        [$requestsProductBlock, $requestsGrand] = $this->buildRequestsProductBlock($tomorrow, $fdIndex);

        if (!empty($requestsProductBlock['items'])) {
            // Add synthetic "On-demand Requests" card into tomorrow products
            $tomorrowEstimate['products']['__requests__'] = $requestsProductBlock;

            // Grand total should include priced flower lines from requests
            $tomorrowEstimate['grand_total_amount'] = round(
                (float) ($tomorrowEstimate['grand_total_amount'] ?? 0) + (float) $requestsGrand,
                2
            );

            // Recompute Totals By Item to include requests
            $tomorrowEstimate['totals_by_item'] = $this->recomputeTotalsByItemFromProducts($tomorrowEstimate['products']);
        }

        // ---- Build daily numbers + RANGE GRAND TOTALS -----------------------
        $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
        $dailyEstimates = [];

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

            $dayTotalsByItemBase = [];

            foreach ($byProduct as $productId => $subsForProduct) {
                $product   = optional($subsForProduct->first())->flowerProducts;
                $subsCount = $subsForProduct->count();

                $items        = [];
                $productTotal = 0.0;

                if ($product) {
                    foreach ($product->packageItems as $pi) {
                        $perItemQty = (float) ($pi->quantity ?? 0);
                        $origUnit   = strtolower(trim((string) $pi->unit));

                        // ----- CATEGORY & QTY (base) --------------------------------
                        $category     = $this->inferCategory($origUnit);
                        if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                        $toBaseFactor = $this->toBaseFactor($origUnit); // item unit → base
                        $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                        // ----- DYNAMIC PRICING from FlowerDetails -------------------
                        $nameKey = strtolower(trim((string) $pi->item_name));
                        $fd      = $fdIndex->get($nameKey);
                        $itemPricePerSub = 0.0;

                        if ($fd) {
                            $fdUnit   = strtolower(trim((string) $fd->unit));
                            $fdPrice  = (float) $fd->price;

                            $perSubQtyBase  = $perItemQty * $this->toBaseFactor($origUnit);
                            $fdUnitBase     = $this->toBaseFactor($fdUnit) ?: 1.0;

                            $fdUnitsCount   = $perSubQtyBase / $fdUnitBase;
                            $itemPricePerSub = $fdPrice * $fdUnitsCount;
                        }

                        $totalPrice = $itemPricePerSub * $subsCount;

                        $items[] = [
                            'item_name'          => $pi->item_name,
                            'category'           => $category,
                            'per_item_qty'       => $perItemQty,
                            'per_item_unit'      => $origUnit,
                            'item_price_per_sub' => round($itemPricePerSub, 2),
                            'total_qty_base'     => $totalQtyBase,
                            'total_qty_disp'     => $qtyDisp,
                            'total_unit_disp'    => $unitDisp,
                            'total_price'        => round($totalPrice, 2),
                        ];

                        $productTotal += $totalPrice;

                        // --- aggregate to day totals (by item)
                        $key = strtolower($pi->item_name) . '|' . $category;
                        if (!isset($dayTotalsByItemBase[$key])) {
                            $dayTotalsByItemBase[$key] = [
                                'item_name'      => $pi->item_name,
                                'category'       => $category,
                                'total_qty_base' => 0.0,
                            ];
                        }
                        $dayTotalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;

                        // --- aggregate to RANGE totals
                        if (!isset($rangeTotalsByItemBase[$key])) {
                            $rangeTotalsByItemBase[$key] = [
                                'item_name'      => $pi->item_name,
                                'category'       => $category,
                                'total_qty_base' => 0.0,
                            ];
                        }
                        $rangeTotalsByItemBase[$key]['total_qty_base'] += $totalQtyBase;
                        $rangeTotalsByCategoryBase[$category] += $totalQtyBase;
                    }
                }

                $grandTotalForDay += $productTotal;

                $productsForDay[$productId] = [
                    'product'              => $product,
                    'subs_count'           => $subsCount,
                    'items'                => $items,
                    'product_total'        => round($productTotal, 2),
                    'bundle_total_per_sub' => round(array_sum(array_column($items, 'item_price_per_sub')), 2),
                ];
            }

            $dayTotalsForDisplay = $this->formatTotalsByItem($dayTotalsByItemBase);

            $dailyEstimates[$day->toDateString()] = [
                'products'           => $productsForDay,
                'grand_total_amount' => round($grandTotalForDay, 2),
                'totals_by_item'     => $dayTotalsForDisplay,
            ];
        }

        // ---- Month-wise rollup ----------------------------------------------
        $monthlyEstimates = [];
        if ($mode === 'month') {
            foreach ($dailyEstimates as $dateStr => $payload) {
                $monthKey = Carbon::parse($dateStr)->format('Y-m');

                if (!isset($monthlyEstimates[$monthKey])) {
                    $monthlyEstimates[$monthKey] = [
                        'month_label'        => Carbon::parse($dateStr)->format('M Y'),
                        'products'           => [],
                        'grand_total'        => 0.0,
                        'totals_by_item_base'=> [],
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
                        $key = strtolower($it['item_name']) . '|' . $it['category'];

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
                    $monthlyEstimates[$monthKey]['grand_total']                      += $row['product_total'];
                }
            }

            foreach ($monthlyEstimates as &$mBlock) {
                foreach ($mBlock['products'] as &$pBlock) {
                    foreach ($pBlock['items'] as &$iBlock) {
                        [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase(
                            $iBlock['total_qty_base'],
                            $iBlock['category']
                        );
                        $iBlock['total_qty_disp']  = $qtyDisp;
                        $iBlock['total_unit_disp'] = $unitDisp;
                        $iBlock['total_price']     = round($iBlock['total_price'], 2);
                    }
                    $pBlock['product_total'] = round($pBlock['product_total'], 2);
                }
                $mBlock['totals_by_item'] = $this->formatTotalsByItem($mBlock['totals_by_item_base']);
                unset($mBlock['totals_by_item_base']);
                $mBlock['grand_total'] = round($mBlock['grand_total'], 2);
            }
            unset($mBlock, $pBlock, $iBlock);
        }

        // ---- RANGE GRAND TOTALS (display) -----------------------------------
        $rangeTotals = [
            'by_item'     => $this->formatTotalsByItem($rangeTotalsByItemBase),
            'by_category' => $this->formatTotalsByCategory($rangeTotalsByCategoryBase),
        ];

        // ======= Lookups for the "Assign Vendor" modal =======================
        $vendors = FlowerVendor::select('vendor_id', 'vendor_name')->orderBy('vendor_name')->get();
        $riders  = RiderDetails::select('rider_id', 'rider_name')->orderBy('rider_name')->get();
        $flowers = FlowerProduct::select('product_id', 'name')->orderBy('name')->get();
        $units   = PoojaUnit::select('id', 'unit_name')->orderBy('unit_name')->get();

        $flowerNameToId = $flowers->pluck('product_id', 'name')->toArray();

        $normalizeUnit = function (?string $raw): string {
            $u = strtolower(trim((string) $raw));
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
        };

        $unitSymbolToId = [];
        foreach ($units as $u) {
            $sym = $normalizeUnit($u->unit_name);
            if (!isset($unitSymbolToId[$sym])) {
                $unitSymbolToId[$sym] = $u->id;
            }
        }

        return view('admin.reports.flower-estimates', [
            'start'            => $start->toDateString(),
            'end'              => $end->toDateString(),
            'mode'             => $mode,
            'preset'           => $preset,
            'dailyEstimates'   => $dailyEstimates,
            'monthlyEstimates' => $monthlyEstimates,
            'tomorrowDate'     => $tomorrow->toDateString(),
            'tomorrowEstimate' => $tomorrowEstimate,
            'rangeTotals'      => $rangeTotals,
            'vendors'          => $vendors,
            'riders'           => $riders,
            'flowers'          => $flowers,
            'units'            => $units,
            'flowerNameToId'   => $flowerNameToId,
            'unitSymbolToId'   => $unitSymbolToId,
        ]);
    }

    private function fetchRequestsForDate(Carbon $date): Collection
    {
        return FlowerRequest::with('flowerRequestItems')
            ->whereDate('date', $date->toDateString())
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get();
    }

    private function buildRequestsProductBlock(Carbon $date, Collection $fdIndex): array
    {
        $requests   = $this->fetchRequestsForDate($date);
        $items      = [];
        $grandTotal = 0.0;

        foreach ($requests as $req) {
            foreach ($req->flowerRequestItems as $ri) {
                $type = strtolower(trim((string) $ri->type));

                if ($type === 'garland') {
                    // Garlands → treat as count, no price
                    $name             = trim((string) ($ri->garland_name ?? 'Garland'));
                    $perQty           = (float) ($ri->garland_quantity ?? 0);
                    $origUnit         = 'pcs';
                    $category         = 'count';
                    $toBaseFactor     = $this->toBaseFactor($origUnit);
                    $totalQtyBase     = $perQty * $toBaseFactor;
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                    $items[] = [
                        'item_name'          => $name,
                        'category'           => $category,
                        'per_item_qty'       => $perQty,
                        'per_item_unit'      => $origUnit,
                        'item_price_per_sub' => 0.00,         // keeps table shape consistent
                        'total_qty_base'     => $totalQtyBase,
                        'total_qty_disp'     => $qtyDisp,
                        'total_unit_disp'    => $unitDisp,
                        'total_price'        => 0.00,
                    ];
                    continue;
                }

                // Flowers or other types → price via FlowerDetails if available
                $name       = trim((string) ($ri->flower_name ?? 'Flower'));
                $perQty     = (float) ($ri->flower_quantity ?? 0);
                $origUnit   = strtolower(trim((string) ($ri->flower_unit ?? 'pcs')));
                $category   = $this->inferCategory($origUnit);
                if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }

                $toBaseFactor = $this->toBaseFactor($origUnit);
                $totalQtyBase = $perQty * $toBaseFactor;
                [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                $totalPrice = 0.0;
                // Dynamic pricing from FlowerDetails live index (name → {unit,price})
                $fd = $fdIndex->get(strtolower($name));
                if ($fd) {
                    $fdUnit     = strtolower(trim((string) $fd->unit));
                    $fdPrice    = (float) $fd->price;
                    $fdUnitBase = $this->toBaseFactor($fdUnit) ?: 1.0;

                    // how many priced-units fit in this request quantity
                    $fdUnitsCount = $totalQtyBase / $fdUnitBase;
                    $totalPrice   = $fdPrice * $fdUnitsCount;
                }

                $items[] = [
                    'item_name'          => $name,
                    'category'           => $category,
                    'per_item_qty'       => $perQty,
                    'per_item_unit'      => $origUnit,
                    'item_price_per_sub' => 0.00, // semantic: per-request line (not bundles)
                    'total_qty_base'     => $totalQtyBase,
                    'total_qty_disp'     => $qtyDisp,
                    'total_unit_disp'    => $unitDisp,
                    'total_price'        => round($totalPrice, 2),
                ];

                $grandTotal += $totalPrice;
            }
        }

        $productBlock = [
            'product'              => (object) ['name' => 'On-demand Requests'],
            'subs_count'           => $requests->count(),   // number of requests that day
            'items'                => $items,
            'product_total'        => round($grandTotal, 2),
            'bundle_total_per_sub' => 0.00,                 // not applicable to requests
        ];

        return [$productBlock, round($grandTotal, 2)];
    }

    private function recomputeTotalsByItemFromProducts(array $products): array
    {
        $base = [];
        foreach ($products as $row) {
            foreach (($row['items'] ?? []) as $it) {
                $key = strtolower($it['item_name']) . '|' . $it['category'];
                if (!isset($base[$key])) {
                    $base[$key] = [
                        'item_name'      => $it['item_name'],
                        'category'       => $it['category'],
                        'total_qty_base' => 0.0,
                    ];
                }
                $base[$key]['total_qty_base'] += (float) ($it['total_qty_base'] ?? 0);
            }
        }
        return $this->formatTotalsByItem($base);
    }

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
            ->whereDate(DBFacade::raw('COALESCE(new_date, end_date)'), '>=', $date->toDateString())
            ->get();

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

    private function buildEstimateForSubsOnDate($subs, Carbon $date, \Illuminate\Support\Collection $fdIndex): array
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
                    $perItemQty = (float) ($pi->quantity ?? 0);
                    $origUnit   = strtolower(trim((string) $pi->unit));

                    $category     = $this->inferCategory($origUnit);
                    if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                    $toBaseFactor = $this->toBaseFactor($origUnit);
                    $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                    // Dynamic price from FlowerDetails
                    $nameKey = strtolower(trim((string) $pi->item_name));
                    $fd      = $fdIndex->get($nameKey);
                    $itemPricePerSub = 0.0;

                    if ($fd) {
                        $fdUnit  = strtolower(trim((string) $fd->unit));
                        $fdPrice = (float) $fd->price;

                        $perSubQtyBase = $perItemQty * $this->toBaseFactor($origUnit);
                        $fdUnitBase    = $this->toBaseFactor($fdUnit) ?: 1.0;

                        $fdUnitsCount   = $perSubQtyBase / $fdUnitBase;
                        $itemPricePerSub = $fdPrice * $fdUnitsCount;
                    }

                    $totalPrice = $itemPricePerSub * $subsCount;

                    $items[] = [
                        'item_name'          => $pi->item_name,
                        'category'           => $category,
                        'per_item_qty'       => $perItemQty,
                        'per_item_unit'      => $origUnit,
                        'item_price_per_sub' => round($itemPricePerSub, 2),
                        'total_qty_base'     => $totalQtyBase,
                        'total_qty_disp'     => $qtyDisp,
                        'total_unit_disp'    => $unitDisp,
                        'total_price'        => round($totalPrice, 2),
                    ];

                    $productTotal += $totalPrice;

                    $key = strtolower($pi->item_name) . '|' . $category;
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
                'product_total'        => round($productTotal, 2),
                'bundle_total_per_sub' => round(array_sum(array_column($items, 'item_price_per_sub')), 2),
            ];
        }

        return [
            'date'               => $date->toDateString(),
            'products'           => $productsForDay,
            'grand_total_amount' => round($grandTotalForDay, 2),
            'totals_by_item'     => $this->formatTotalsByItem($dayTotalsByItemBase),
        ];
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
        uasort($rows, fn ($a, $b) => strcasecmp($a['item_name'], $b['item_name']));
        return $rows;
    }

    private function formatTotalsByCategory(array $baseByCat): array
    {
        $out = [];
        foreach (['weight','volume','count'] as $cat) {
            [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($baseByCat[$cat] ?? 0, $cat);
            $label = match ($cat) {
                'weight' => 'Weight',
                'volume' => 'Volume',
                default  => 'Count',
            };
            $out[] = [
                'label'           => $label,
                'category'        => $cat,
                'total_qty_base'  => (float) ($baseByCat[$cat] ?? 0),
                'total_qty_disp'  => $qtyDisp,
                'total_unit_disp' => $unitDisp,
            ];
        }
        return $out;
    }
}
