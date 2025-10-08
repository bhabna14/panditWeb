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
        $dateStr   = $request->input('date',  Carbon::today()->toDateString());
        $monthStr  = $request->input('month', Carbon::today()->format('Y-m'));
        $pdpFilter = $request->input('per_day_price', 'all');

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Only Subscription category
        $subProdQ = FlowerProduct::query()
            ->select('product_id','name','category','per_day_price','status')
            ->whereRaw('LOWER(category) = ?', ['subscription']);

        // Dropdown options (Subscription only)
        $perDayPriceOptions = (clone $subProdQ)
            ->whereNotNull('per_day_price')
            ->distinct()
            ->orderBy('per_day_price')
            ->pluck('per_day_price')
            ->values();

        // Apply filter
        $subProdFilteredQ = clone $subProdQ;
        if ($pdpFilter === 'has') {
            $subProdFilteredQ->whereNotNull('per_day_price');
        } elseif ($pdpFilter !== 'all' && is_numeric($pdpFilter)) {
            $subProdFilteredQ->where('per_day_price', (float)$pdpFilter);
        }
        $subProducts = $subProdFilteredQ->get();

        $subsByProductId = $subProducts->keyBy('product_id');
        $subscriptionProductIds = $subProducts->pluck('product_id')->all();

        $dayEstimate   = $this->estimateForDate($date, $subscriptionProductIds, $subsByProductId);
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

        $perDay = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $perDay[$cursor->toDateString()] = $this->tallyPackageItemsForDay($subs, $subsByProductId, $cursor);
            $cursor->addDay();
        }

        // Aggregate (Unit Price Total only)
        $byItem = [];
        $unitPriceGrandTotal = 0.0;

        foreach ($perDay as $data) {
            foreach ($data['lines'] as $key => $line) {
                if (!isset($byItem[$key])) {
                    $byItem[$key] = [
                        'item_name'  => $line['item_name'],
                        'unit'       => $line['unit'],
                        'unit_price' => $line['unit_price'], // per-unit (derived)
                        'qty'        => 0.0,
                    ];
                }
                // keep qty just for display (not used in totals)
                $byItem[$key]['qty'] += $line['qty'];

                // month total = sum of per-unit prices (one per item key)
                // To be consistent with day view (one row per key), we add day's unit price only once per day.
                $unitPriceGrandTotal += $line['unit_price'];
            }
        }

        uasort($byItem, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'per_day'    => $perDay,
            'by_item'    => $byItem,
            'total_cost' => round($unitPriceGrandTotal, 2), // now means Unit Price Total
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
     * Day tally:
     * - Convert bundle -> per-unit price (price / quantity).
     * - Keep qty for info, but "total_cost" is the sum of unit prices across items (no qty multiplication).
     * - Also keep by_product (unchanged) for visibility.
     */
    protected function tallyPackageItemsForDay(
        Collection $subscriptions,
        Collection $subsByProductId,
        Carbon $day
    ): array {
        $date = $day->copy()->startOfDay();

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
            return ['lines' => [], 'total_cost' => 0.0, 'by_product' => []];
        }

        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $pkgItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $lines = [];     // item rows
        $unitPriceTotal = 0.0;
        $byProduct = []; // still useful for checking; not part of total

        foreach ($deliveries->groupBy('product_id') as $productId => $subsForProduct) {
            $subProd = $subsByProductId->get($productId);
            if (!$subProd) continue;

            $pkgItems = $pkgItemsByProduct->get($productId) ?? collect();

            // by-product info (unchanged)
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
                    ];
                }

                // qty is informational only (sum of bundles across subs)
                $lines[$key]['qty'] += $bundleQty * $subsCount;

                // total now sums ONLY unit prices (once per item key)
                // Since lines are unique by key per day, add once here.
                // If the same key appears from multiple products (unlikely), it's already grouped.
                // To avoid double-adding when loop continues, only add when the line is first created.
                if ($lines[$key]['qty'] == ($bundleQty * $subsCount)) {
                    $unitPriceTotal += $lines[$key]['unit_price'];
                }
            }
        }

        uasort($lines, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));

        return [
            'lines'      => $lines,
            'total_cost' => round($unitPriceTotal, 2), // now "Unit Price Total"
            'by_product' => $byProduct,
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
}
