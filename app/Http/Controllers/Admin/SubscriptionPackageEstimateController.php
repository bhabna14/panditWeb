<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Models\PackageItem;

class SubscriptionPackageEstimateController extends Controller
{
    public function index(Request $request)
    {
        // ---- Inputs ---------------------------------------------------------
        $today = Carbon::today();

        $fromStr = $request->input('from_date');
        $toStr   = $request->input('to_date');

        // Default: today -> today
        if (!$fromStr && !$toStr) {
            $fromStr = $today->toDateString();
            $toStr   = $today->toDateString();
        } elseif ($fromStr && !$toStr) {
            $toStr = $fromStr;
        } elseif (!$fromStr && $toStr) {
            $fromStr = $toStr;
        }

        $fromDate = Carbon::parse($fromStr)->startOfDay();
        $toDate   = Carbon::parse($toStr)->endOfDay();

        // If reversed, swap
        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [
                $toDate->copy()->startOfDay(),
                $fromDate->copy()->endOfDay(),
            ];
        }

        // Month for month summary – default to fromDate's month
        $monthStr  = $request->input('month', $fromDate->format('Y-m'));
        $pdpFilter = $request->input('per_day_price', 'all');

        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // --- Only Subscription category -------------------------------------
        $subProdQ = FlowerProduct::query()
            ->select('product_id', 'name', 'category', 'per_day_price', 'status')
            ->whereRaw('LOWER(category) = ?', ['subscription']);

        // Dropdown options: ONLY Subscription products
        $perDayPriceOptions = (clone $subProdQ)
            ->whereNotNull('per_day_price')
            ->distinct()
            ->orderBy('per_day_price')
            ->pluck('per_day_price')
            ->values();

        // Apply chosen per_day_price filter
        $subProdFilteredQ = clone $subProdQ;

        if ($pdpFilter === 'has') {
            $subProdFilteredQ->whereNotNull('per_day_price');
        } elseif ($pdpFilter !== 'all' && is_numeric($pdpFilter)) {
            $subProdFilteredQ->where('per_day_price', (float) $pdpFilter);
        }

        $subProducts = $subProdFilteredQ->get();

        // Lookups
        $subsByProductId        = $subProducts->keyBy('product_id');
        $subscriptionProductIds = $subProducts->pluck('product_id')->all();

        // ---- Range + Month estimates ---------------------------------------
        $rangeEstimate = $this->estimateRangeSummary(
            $fromDate,
            $toDate,
            $subscriptionProductIds,
            $subsByProductId
        );

        $monthEstimate = $this->estimateForRange(
            $monthStart,
            $monthEnd,
            $subscriptionProductIds,
            $subsByProductId
        );

        return view('admin.reports.subscription-package-estimates', [
            'fromDate'           => $fromDate,
            'toDate'             => $toDate,
            'monthStart'         => $monthStart,

            'selectedFromDate'   => $fromDate->toDateString(),
            'selectedToDate'     => $toDate->toDateString(),
            'selectedMonth'      => $monthStart->format('Y-m'),

            'perDayPriceOptions' => $perDayPriceOptions,
            'selectedPdp'        => $pdpFilter,

            'rangeEstimate'      => $rangeEstimate,
            'monthEstimate'      => $monthEstimate,
        ]);
    }

    /**
     * (Optional) keep this if you use it elsewhere
     */
    protected function estimateForDate(
        Carbon $date,
        array $subscriptionProductIds,
        Collection $subsByProductId
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($date, $date, $subscriptionProductIds);
        return $this->tallyPackageItemsForDay($subs, $subsByProductId, $date);
    }

    /**
     * Range summary for the top "Range Summary" card
     * Aggregates:
     *  - lines (per item)
     *  - by_product (overall range)
     *  - by_product_items (price list per subscription)
     *  - per_product_per_day (PRODUCT-WISE + DATE-WISE breakdown)
     */
    protected function estimateRangeSummary(
        Carbon $start,
        Carbon $end,
        array $subscriptionProductIds,
        Collection $subsByProductId
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($start, $end, $subscriptionProductIds);

        $lines             = [];
        $byProduct         = [];
        $byProductItems    = [];
        $perProductPerDay  = [];
        $totalCost         = 0.0;

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dayData = $this->tallyPackageItemsForDay($subs, $subsByProductId, $cursor);
            $dateKey = $cursor->toDateString();

            // Aggregate item lines
            foreach ($dayData['lines'] as $key => $line) {
                if (!isset($lines[$key])) {
                    $lines[$key] = $line;
                } else {
                    $lines[$key]['qty']      += $line['qty'];
                    $lines[$key]['subtotal'] += $line['subtotal'];
                }
            }

            // Aggregate by product + product-wise per-day breakdown
            foreach ($dayData['by_product'] ?? [] as $productId => $row) {
                // Overall product summary
                if (!isset($byProduct[$productId])) {
                    $byProduct[$productId] = [
                        'product_name'  => $row['product_name'],
                        'subscriptions' => 0,
                        'bundle_total'  => $row['bundle_total'], // per-sub bundle
                        'subtotal'      => 0.0,
                    ];
                }
                $byProduct[$productId]['subscriptions'] += $row['subscriptions'];
                $byProduct[$productId]['subtotal']      += $row['subtotal'];

                // Product-wise per-day
                if (!isset($perProductPerDay[$productId])) {
                    $perProductPerDay[$productId] = [
                        'product_name' => $row['product_name'],
                        'days'         => [],
                    ];
                }
                if (!isset($perProductPerDay[$productId]['days'][$dateKey])) {
                    $perProductPerDay[$productId]['days'][$dateKey] = [
                        'date'          => $dateKey,
                        'subscriptions' => 0,
                        'bundle_total'  => $row['bundle_total'],
                        'subtotal'      => 0.0,
                    ];
                }
                $perProductPerDay[$productId]['days'][$dateKey]['subscriptions'] += $row['subscriptions'];
                $perProductPerDay[$productId]['days'][$dateKey]['subtotal']      += $row['subtotal'];
            }

            // Price-list per subscription (doesn't vary with date, so first non-empty is ok)
            if (empty($byProductItems) && !empty($dayData['by_product_items'] ?? [])) {
                $byProductItems = $dayData['by_product_items'];
            }

            $totalCost += $dayData['total_cost'] ?? 0.0;

            $cursor->addDay();
        }

        // Sort products nicely
        $byProduct = array_values($byProduct);
        usort($byProduct, fn ($a, $b) => strcasecmp($a['product_name'], $b['product_name']));

        // Sort days inside each product
        foreach ($perProductPerDay as $pid => $prod) {
            ksort($perProductPerDay[$pid]['days']);
        }

        return [
            'lines'              => $lines,
            'by_product'         => $byProduct,
            'by_product_items'   => $byProductItems,
            'per_product_per_day'=> $perProductPerDay,   // <-- product-wise + date-wise
            'total_cost'         => round($totalCost, 2),
        ];
    }

    /**
     * Month (or arbitrary range) estimate by item – same as you already had
     */
    protected function estimateForRange(
        Carbon $start,
        Carbon $end,
        array $subscriptionProductIds,
        Collection $subsByProductId
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($start, $end, $subscriptionProductIds);

        $perDay = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $perDay[$cursor->toDateString()] =
                $this->tallyPackageItemsForDay($subs, $subsByProductId, $cursor);
            $cursor->addDay();
        }

        // Aggregate by item (per-unit math stays the same)
        $byItem    = [];
        $totalQty  = 0.0;
        $totalCost = 0.0;

        foreach ($perDay as $data) {
            foreach ($data['lines'] as $key => $line) {
                if (!isset($byItem[$key])) {
                    $byItem[$key] = [
                        'item_name'  => $line['item_name'],
                        'unit'       => $line['unit'],
                        'unit_price' => $line['unit_price'], // per-unit (derived)
                        'qty'        => 0.0,
                        'subtotal'   => 0.0,
                    ];
                }
                $byItem[$key]['qty']      += $line['qty'];
                $byItem[$key]['subtotal'] += $line['subtotal'];

                $totalQty  += $line['qty'];
                $totalCost += $line['subtotal'];
            }
        }

        uasort($byItem, fn ($a, $b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'per_day'    => $perDay,
            'by_item'    => $byItem,
            'total_qty'  => $totalQty,
            'total_cost' => round($totalCost, 2),
        ];
    }

    protected function activeSubscriptionsOverlapping(Carbon $start, Carbon $end, array $subscriptionProductIds)
    {
        if (empty($subscriptionProductIds)) {
            return collect();
        }

        return Subscription::query()
            ->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_active', 1);
            })
            ->whereIn('product_id', $subscriptionProductIds)
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get([
                'subscription_id','user_id','product_id',
                'start_date','end_date',
                'pause_start_date','pause_end_date',
                'status','is_active'
            ]);
    }

    /**
     * Day tally (bundle -> per-unit math for consumption),
     * plus per-product **price list** (bundle prices) for display.
     */
    protected function tallyPackageItemsForDay(
        Collection $subscriptions,
        Collection $subsByProductId,
        Carbon $day
    ): array {
        $date = $day->copy()->startOfDay();

        // Filter: in window and not paused on that day
        $deliveries = $subscriptions->filter(function ($s) use ($date) {
            $inWindow = Carbon::parse($s->start_date)->startOfDay()->lte($date)
                     && Carbon::parse($s->end_date)->endOfDay()->gte($date);
            $paused = false;
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                        && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
            }
            return $inWindow && !$paused;
        });

        if ($deliveries->isEmpty()) {
            return [
                'lines'            => [],
                'total_qty'        => 0.0,
                'total_cost'       => 0.0,
                'by_product'       => [],
                'by_product_items' => [],
            ];
        }

        // Preload package items for all involved products
        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $pkgItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $lines = [];     // item-level (per-unit math)
        $totalQty = 0.0;
        $totalCost = 0.0;

        $byProduct       = []; // summary by product
        $byProductItems  = []; // price list per product (like modal)

        foreach ($deliveries->groupBy('product_id') as $productId => $subsForProduct) {
            $subProd = $subsByProductId->get($productId);
            if (!$subProd) continue;

            $pkgItems = $pkgItemsByProduct->get($productId) ?? collect();

            // Per-product summary
            $bundleTotal = 0.0;
            foreach ($pkgItems as $it) {
                $bundleTotal += (float) ($it->price ?? 0);
            }
            $subsCount = $subsForProduct->count();
            $byProduct[$productId] = [
                'product_name' => (string) $subProd->name,
                'subscriptions'=> $subsCount,
                'bundle_total' => round($bundleTotal, 2),
                'subtotal'     => round($bundleTotal * $subsCount, 2),
            ];

            // Price list per subscription
            $priceItems = [];
            $priceSum   = 0.0;
            $rowIndex   = 1;
            foreach ($pkgItems as $it) {
                $itemName    = (string) ($it->item_name ?? 'Item');
                $unit        = (string) ($it->unit ?? 'unit');
                $bundleQty   = (float)  ($it->quantity ?? 0);
                $bundlePrice = (float)  ($it->price ?? 0);
                if ($bundleQty <= 0) continue;

                $priceItems[] = [
                    'idx'        => $rowIndex++,
                    'item_name'  => $itemName,
                    'quantity'   => $bundleQty,
                    'unit'       => $unit,
                    'item_price' => round($bundlePrice, 2),
                ];
                $priceSum += $bundlePrice;
            }
            $byProductItems[$productId] = [
                'product_name' => (string) $subProd->name,
                'items'        => $priceItems,
                'total'        => round($priceSum, 2),
            ];

            // Item aggregation (per-unit math)
            foreach ($pkgItems as $it) {
                $itemName   = (string) ($it->item_name ?? 'Item');
                $unit       = (string) ($it->unit ?? 'unit');
                $bundleQty  = (float)  ($it->quantity ?? 0);
                $bundlePrice= (float)  ($it->price ?? 0);
                if ($bundleQty <= 0) continue;

                $unitPrice  = $bundlePrice / $bundleQty; // derived

                $key = $this->norm($itemName) . '|' . strtolower($unit);
                if (!isset($lines[$key])) {
                    $lines[$key] = [
                        'item_name'  => $itemName,
                        'unit'       => $unit,
                        'unit_price' => round($unitPrice, 4),
                        'qty'        => 0.0,
                        'subtotal'   => 0.0,
                    ];
                }

                $addedQty = $bundleQty * $subsCount;
                $lines[$key]['qty']      += $addedQty;
                $lines[$key]['subtotal']  = round($lines[$key]['qty'] * $lines[$key]['unit_price'], 2);

                $totalQty  += $addedQty;
                $totalCost += ($addedQty * $unitPrice);
            }
        }

        uasort($lines, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'lines'            => $lines,
            'total_qty'        => $totalQty,
            'total_cost'       => round($totalCost, 2),
            'by_product'       => $byProduct,
            'by_product_items' => $byProductItems,
        ];
    }

    protected function norm(?string $s): string
    {
        return Str::of($s ?? '')
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    // ========== CSV export UPDATED to use from_date / to_date + product-wise breakdown ==========
    public function exportCsv(Request $request)
    {
        $today   = Carbon::today();
        $fromStr = $request->input('from_date');
        $toStr   = $request->input('to_date');

        // Default: today -> today
        if (!$fromStr && !$toStr) {
            $fromStr = $today->toDateString();
            $toStr   = $today->toDateString();
        } elseif ($fromStr && !$toStr) {
            $toStr = $fromStr;
        } elseif (!$fromStr && $toStr) {
            $fromStr = $toStr;
        }

        $fromDate = Carbon::parse($fromStr)->startOfDay();
        $toDate   = Carbon::parse($toStr)->endOfDay();

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [
                $toDate->copy()->startOfDay(),
                $fromDate->copy()->endOfDay(),
            ];
        }

        $monthStr  = $request->input('month', $fromDate->format('Y-m'));
        $pdpFilter = $request->input('per_day_price', 'all');

        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Products (Subscription only) with filter
        $subProdQ = FlowerProduct::query()
            ->select('product_id','name','category','per_day_price','status')
            ->whereRaw('LOWER(category) = ?', ['subscription']);

        if ($pdpFilter === 'has') {
            $subProdQ->whereNotNull('per_day_price');
        } elseif ($pdpFilter !== 'all' && is_numeric($pdpFilter)) {
            $subProdQ->where('per_day_price', (float) $pdpFilter);
        }

        $subProducts          = $subProdQ->get();
        $subsByProductId      = $subProducts->keyBy('product_id');
        $subscriptionProductIds = $subProducts->pluck('product_id')->all();

        $rangeEstimate = $this->estimateRangeSummary($fromDate, $toDate, $subscriptionProductIds, $subsByProductId);
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $subscriptionProductIds, $subsByProductId);

        $filename = "subscription_pkg_estimates_{$fromDate->toDateString()}_to_{$toDate->toDateString()}_month_{$monthStart->format('Y-m')}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($fromDate, $toDate, $rangeEstimate, $monthStart, $monthEstimate, $pdpFilter) {
            $out = fopen('php://output', 'w');

            // Filter info
            fputcsv($out, ['Subscription Package Estimates']);
            fputcsv($out, ['Per-Day Price Filter', $pdpFilter]);
            fputcsv($out, ['Range From', $fromDate->toDateString(), 'Range To', $toDate->toDateString()]);
            fputcsv($out, []);

            // Range - Product Summary
            fputcsv($out, ['Range Product Summary']);
            fputcsv($out, ['Product', 'Subscriptions (sum)', 'Bundle Total / sub', 'Subtotal']);
            foreach ($rangeEstimate['by_product'] as $row) {
                fputcsv($out, [
                    $row['product_name'],
                    $row['subscriptions'],
                    $row['bundle_total'],
                    $row['subtotal'],
                ]);
            }
            fputcsv($out, ['Range Total', '', '', $rangeEstimate['total_cost']]);
            fputcsv($out, []);

            // Range - Product-wise Day Breakdown
            fputcsv($out, ['Range Product-wise Day Breakdown']);
            fputcsv($out, ['Product', 'Date', 'Subscriptions', 'Bundle Total / sub', 'Subtotal']);
            foreach ($rangeEstimate['per_product_per_day'] as $prod) {
                $pName = $prod['product_name'];
                foreach ($prod['days'] as $row) {
                    fputcsv($out, [
                        $pName,
                        $row['date'],
                        $row['subscriptions'],
                        $row['bundle_total'],
                        $row['subtotal'],
                    ]);
                }
            }
            fputcsv($out, []);

            // Month (per item)
            fputcsv($out, ["Month-wise Estimate", $monthStart->format('Y-m')]);
            fputcsv($out, ['Item','Unit','Total Qty','Unit Price (per unit)','Subtotal']);
            foreach ($monthEstimate['by_item'] as $row) {
                fputcsv($out, [
                    $row['item_name'],
                    $row['unit'],
                    $row['qty'],
                    $row['unit_price'],
                    $row['subtotal'],
                ]);
            }
            fputcsv($out, ['Month Totals','','','', $monthEstimate['total_cost']]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
