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
        // Inputs
        $dateStr   = $request->input('date',  Carbon::today()->toDateString());
        $monthStr  = $request->input('month', Carbon::today()->format('Y-m'));
        // per-day-price filter on Subscription products: "all" | "has" | <numeric exact value>
        $pdpFilter = $request->input('per_day_price', 'all');

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // --- Base query: only category = "Subscription"
        $subProdQ = FlowerProduct::query()
            ->select('product_id','name','category','per_day_price','status')
            ->whereRaw('LOWER(category) = ?', ['subscription']);

        // Build distinct per_day_price options for the dropdown
        $perDayPriceOptions = (clone $subProdQ)
            ->whereNotNull('per_day_price')
            ->distinct()
            ->orderBy('per_day_price')
            ->pluck('per_day_price')
            ->values();

        // Apply the chosen per_day_price filter
        $subProdFilteredQ = clone $subProdQ;
        if ($pdpFilter === 'has') {
            $subProdFilteredQ->whereNotNull('per_day_price');
        } elseif ($pdpFilter !== 'all' && is_numeric($pdpFilter)) {
            $subProdFilteredQ->where('per_day_price', (float)$pdpFilter);
        }
        $subProducts = $subProdFilteredQ->get();

        // Fast lookup for product, plus list of product_ids
        $subsByProductId = $subProducts->keyBy('product_id');
        $subscriptionProductIds = $subProducts->pluck('product_id')->all();

        // ---- Day-wise estimate (selected day) ----
        $dayEstimate = $this->estimateForDate($date, $subscriptionProductIds, $subsByProductId);

        // ---- Month-wise estimate (aggregate + per-day) ----
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $subscriptionProductIds, $subsByProductId);

        return view('admin.reports.subscription-package-estimates', [
            'date'                => $date,
            'monthStart'          => $monthStart,
            'selectedDate'        => $date->toDateString(),
            'selectedMonth'       => $monthStart->format('Y-m'),
            'perDayPriceOptions'  => $perDayPriceOptions,
            'selectedPdp'         => $pdpFilter,
            'dayEstimate'         => $dayEstimate,
            'monthEstimate'       => $monthEstimate,
        ]);
    }

    // ========= Core Estimation (Subscription products; expand PackageItem) =========

    protected function estimateForDate(
        Carbon $date,
        array $subscriptionProductIds,
        Collection $subsByProductId
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($date, $date, $subscriptionProductIds);
        return $this->tallyPackageItemsForDay($subs, $subsByProductId, $date);
    }

    protected function estimateForRange(
        Carbon $start,
        Carbon $end,
        array $subscriptionProductIds,
        Collection $subsByProductId
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($start, $end, $subscriptionProductIds);

        // Build per-day tallies
        $perDay = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $perDay[$cursor->toDateString()] = $this->tallyPackageItemsForDay($subs, $subsByProductId, $cursor);
            $cursor->addDay();
        }

        // Aggregate by item (name + unit)
        $byItem = [];
        $totalQty = 0.0;
        $totalCost = 0.0;

        foreach ($perDay as $data) {
            foreach ($data['lines'] as $key => $line) {
                if (!isset($byItem[$key])) {
                    $byItem[$key] = [
                        'item_name'  => $line['item_name'],
                        'unit'       => $line['unit'],
                        // Keep per-unit price here too (derived)
                        'unit_price' => $line['unit_price'],
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

        uasort($byItem, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'per_day'   => $perDay,
            'by_item'   => $byItem,
            'total_qty' => $totalQty,
            'total_cost'=> $totalCost,
        ];
    }

    /**
     * Active subscriptions overlapping a date range, limited to Subscription products we filtered.
     */
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
     * For a given day:
     *   - Expand package items from product__package_item (item_name, quantity, unit, price).
     *   - IMPORTANT: price is the BUNDLE price for the given quantity (not per-unit).
     *   - We derive per-unit price = price / quantity for correct math when aggregating quantities.
     *   - Each active subscription contributes those items once per day.
     */
    protected function tallyPackageItemsForDay(
        Collection $subscriptions,
        Collection $subsByProductId,
        Carbon $day
    ): array {
        $date = $day->copy()->startOfDay();

        // Filter out subscriptions paused on this day
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
            return ['lines' => [], 'total_qty' => 0.0, 'total_cost' => 0.0];
        }

        // Preload all package items for products involved today
        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $pkgItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $lines = [];   // keyed by normalized item_name + unit
        $totalQty = 0.0;
        $totalCost = 0.0;

        foreach ($deliveries as $sub) {
            // must be a subscription product we selected
            $subProd = $subsByProductId->get($sub->product_id);
            if (!$subProd) continue;

            $pkgItems = $pkgItemsByProduct->get($sub->product_id) ?? collect();
            foreach ($pkgItems as $it) {
                $itemName   = (string) ($it->item_name ?? 'Item');
                $unit       = (string) ($it->unit ?? 'unit');
                $bundleQty  = (float)  ($it->quantity ?? 0);   // quantity for which the price applies
                $bundlePrice= (float)  ($it->price ?? 0);      // price for the above quantity

                if ($bundleQty <= 0) continue;

                // Derive per-unit price (the key change)
                $unitPrice  = $bundlePrice / $bundleQty;

                $key = $this->norm($itemName) . '|' . strtolower($unit);
                if (!isset($lines[$key])) {
                    $lines[$key] = [
                        'item_name'  => $itemName,
                        'unit'       => $unit,
                        'unit_price' => round($unitPrice, 4), // keep more precision per unit
                        'qty'        => 0.0,
                        'subtotal'   => 0.0,
                    ];
                }

                // Per subscription per day: add bundle qty; subtotal grows by per-unit * qty
                $lines[$key]['qty']      += $bundleQty;
                $lines[$key]['subtotal']  = round($lines[$key]['qty'] * $lines[$key]['unit_price'], 2);

                $totalQty  += $bundleQty;
                $totalCost += ($bundleQty * $unitPrice);
            }
        }

        uasort($lines, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'lines'      => $lines,
            'total_qty'  => $totalQty,
            'total_cost' => round($totalCost, 2),
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

    // ========= CSV Export =========

    public function exportCsv(Request $request)
    {
        $dateStr   = $request->input('date',  Carbon::today()->toDateString());
        $monthStr  = $request->input('month', Carbon::today()->format('Y-m'));
        $pdpFilter = $request->input('per_day_price', 'all');

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        $subProdQ = FlowerProduct::query()
            ->select('product_id','name','category','per_day_price','status')
            ->whereRaw('LOWER(category) = ?', ['subscription']);

        if ($pdpFilter === 'has') {
            $subProdQ->whereNotNull('per_day_price');
        } elseif ($pdpFilter !== 'all' && is_numeric($pdpFilter)) {
            $subProdQ->where('per_day_price', (float)$pdpFilter);
        }
        $subProducts = $subProdQ->get();
        $subsByProductId = $subProducts->keyBy('product_id');
        $subscriptionProductIds = $subProducts->pluck('product_id')->all();

        $dayEstimate   = $this->estimateForDate($date, $subscriptionProductIds, $subsByProductId);
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $subscriptionProductIds, $subsByProductId);

        $filename = "subscription_pkg_estimates_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($date, $dayEstimate, $monthStart, $monthEstimate, $pdpFilter) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Category', 'Subscription']);
            fputcsv($out, ['Per-Day Price Filter', $pdpFilter]);

            // Day
            fputcsv($out, ["Day-wise Estimate", $date->toDateString()]);
            // make it clear that unit price is per-unit (derived)
            fputcsv($out, ['Item','Unit','Qty','Unit Price (per unit)','Subtotal']);
            foreach ($dayEstimate['lines'] as $row) {
                fputcsv($out, [
                    $row['item_name'],
                    $row['unit'],
                    $row['qty'],
                    $row['unit_price'],
                    $row['subtotal'],
                ]);
            }
            fputcsv($out, ['Totals','','','', $dayEstimate['total_cost']]);
            fputcsv($out, []);

            // Month
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
