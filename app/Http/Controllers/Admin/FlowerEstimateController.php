<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Models\PackageItem;

class FlowerEstimateController extends Controller
{
    public function index(Request $request)
    {
        // Inputs (defaults to "today" and "this month")
        $dateStr  = $request->input('date', Carbon::today()->toDateString());
        $monthStr = $request->input('month', Carbon::today()->format('Y-m'));

        // NEW filters
        $filterCategory  = $request->input('product_category');           // e.g. 'subscription'
        $filterProductId = $request->input('subscription_product_id');    // a specific subscription product_id

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();
        $tomorrow   = Carbon::tomorrow()->startOfDay();

        // Preload flower products and fast lookup maps
        $allFlowers = FlowerProduct::select(
            'product_id','name','category','price','mrp','per_day_price','discount','status'
        )->get();

        $flowerByProductId = $allFlowers->keyBy('product_id');
        $flowerByNormName  = $allFlowers->keyBy(function ($f) {
            return $this->norm($f->name);
        });

        // Day estimate (using legacy window rules)
        $dayEstimate = $this->estimateForDate($date, $flowerByProductId, $flowerByNormName);

        // Month estimate (using legacy window rules)
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $flowerByProductId, $flowerByNormName);

        // Tomorrow estimate (uses COALESCE(new_date, end_date), excludes expired)
        $tomorrowEstimate = $this->estimateForTomorrow($tomorrow, $flowerByProductId, $flowerByNormName);

        // ===== NEW: Today (selected date) – Active subscriptions per user (with filters) =====
        $perUserToday    = $this->perUserForDate($date, $flowerByProductId, $flowerByNormName, $filterCategory, $filterProductId);

        // ===== NEW: Category-wise summary for today (with the same filters) =====
        $categorySummary = $this->categoryWiseForDate($date, $flowerByProductId, $flowerByNormName, $filterCategory, $filterProductId);

        // ===== NEW: Dropdown data =====
        $allCategories = FlowerProduct::query()
            ->select('category')->distinct()->orderBy('category')
            ->pluck('category')->filter()->values();

        $subscriptionProducts = FlowerProduct::where('category', 'subscription')
            ->orderBy('name')
            ->get(['product_id','name','per_day_price']);

        return view('admin.reports.flower-estimates', [
            'date'              => $date,
            'monthStart'        => $monthStart,
            'dayEstimate'       => $dayEstimate,
            'monthEstimate'     => $monthEstimate,
            'tomorrow'          => $tomorrow,
            'tomorrowEstimate'  => $tomorrowEstimate,
            'selectedDate'      => $date->toDateString(),
            'selectedMonth'     => $monthStart->format('Y-m'),

            // NEW to view
            'filterCategory'       => $filterCategory,
            'filterProductId'      => $filterProductId,
            'allCategories'        => $allCategories,
            'subscriptionProducts' => $subscriptionProducts,
            'perUserToday'         => $perUserToday,
            'categorySummary'      => $categorySummary,
        ]);
    }

    /**
     * Estimate for a single date (legacy rules: end_date only).
     */
    protected function estimateForDate(
        Carbon $date,
        Collection $flowerByProductId,
        Collection $flowerByNormName
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($date, $date);
        return $this->tallyForSubscriptionsOnDate($subs, $date, $flowerByProductId, $flowerByNormName);
    }

    /**
     * Estimate for a date range (legacy rules: end_date only).
     */
    protected function estimateForRange(
        Carbon $start,
        Carbon $end,
        Collection $flowerByProductId,
        Collection $flowerByNormName
    ): array {
        $subs = $this->activeSubscriptionsOverlapping($start, $end);

        $perDay = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $perDay[$cursor->toDateString()] = $this->tallyForSubscriptionsOnDate(
                $subs,
                $cursor,
                $flowerByProductId,
                $flowerByNormName
            );
            $cursor->addDay();
        }

        $byFlower = [];
        $totalQty = 0;
        $totalCost = 0;

        foreach ($perDay as $data) {
            foreach ($data['lines'] as $key => $line) {
                if (!isset($byFlower[$key])) {
                    $byFlower[$key] = [
                        'flower_name' => $line['flower_name'],
                        'unit'        => $line['unit'],
                        'unit_price'  => $line['unit_price'],
                        'qty'         => 0,
                        'subtotal'    => 0,
                    ];
                }
                $byFlower[$key]['qty']      += $line['qty'];
                $byFlower[$key]['subtotal'] += $line['subtotal'];
                $totalQty                   += $line['qty'];
                $totalCost                  += $line['subtotal'];
            }
        }

        uasort($byFlower, fn($a,$b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return [
            'per_day'   => $perDay,
            'by_flower' => $byFlower,
            'total_qty' => $totalQty,
            'total_cost'=> $totalCost,
        ];
    }

    /**
     * Active subscriptions (status 'active' or is_active=1) overlapping the range, using end_date only.
     */
    protected function activeSubscriptionsOverlapping(Carbon $start, Carbon $end)
    {
        return Subscription::query()
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get([
                'subscription_id','order_id','user_id','product_id',
                'start_date','end_date','pause_start_date','pause_end_date',
                'status','is_active','new_date'
            ]);
    }

    /**
     * Tomorrow-specific: use effective end = COALESCE(new_date, end_date), exclude expired.
     * (Still excludes paused for the specific tomorrow date.)
     */
    protected function activeSubscriptionsOverlappingEffective(Carbon $start, Carbon $end)
    {
        return Subscription::query()
            ->where(function ($q) {
                // Explicitly avoid expired; allow active (and optionally paused).
                $q->whereIn('status', ['active', 'paused'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $start->toDateString())
            ->get([
                'subscription_id','order_id','user_id','product_id',
                'start_date','end_date','new_date',
                'pause_start_date','pause_end_date',
                'status','is_active'
            ]);
    }

    protected function estimateForTomorrow(
        Carbon $tomorrow,
        Collection $flowerByProductId,
        Collection $flowerByNormName
    ): array {
        $subs = $this->activeSubscriptionsOverlappingEffective($tomorrow, $tomorrow);
        return $this->tallyForSubscriptionsOnDate($subs, $tomorrow, $flowerByProductId, $flowerByNormName);
    }

    /**
     * For a day, compute the flower requirement from subscriptions.
     * - Direct flower product => +1 unit per subscription per day (adjust if you store per-day qty).
     * - Package product => expand PackageItem rows and add their quantities.
     */
    protected function tallyForSubscriptionsOnDate(
        Collection $subscriptions,
        Carbon $date,
        Collection $flowerByProductId,
        Collection $flowerByNormName
    ): array {
        $deliveries = $subscriptions->filter(function ($s) use ($date) {
            $inWindow = Carbon::parse($s->start_date)->startOfDay()->lte($date)
                      && Carbon::parse(($s->new_date ?? $s->end_date))->endOfDay()->gte($date);

            $paused = false;
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                       && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
            }

            // Exclude expired status outright
            $expired = (isset($s->status) && strtolower((string)$s->status) === 'expired');

            return $inWindow && !$paused && !$expired;
        });

        if ($deliveries->isEmpty()) {
            return [
                'lines'      => [],
                'total_qty'  => 0,
                'total_cost' => 0,
            ];
        }

        // Preload package items for involved product_ids
        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $packageItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $lines = [];
        $totalQty = 0;
        $totalCost = 0;

        foreach ($deliveries as $sub) {
            $product = $flowerByProductId->get($sub->product_id);

            if ($product && strtolower((string) $product->category) === 'flower') {
                // Direct flower: +1 unit per day per subscription
                $this->addFlowerLine($lines, $product->name, 'unit', 1, $this->unitPrice($product), $totalQty, $totalCost);
            } else {
                // Package: expand items
                $items = $packageItemsByProduct->get($sub->product_id) ?? collect();
                foreach ($items as $item) {
                    $norm = $this->norm($item->item_name);
                    $matchedFlower = $flowerByNormName->get($norm);

                    $flowerName = $matchedFlower ? $matchedFlower->name : $item->item_name;
                    $unit       = $item->unit ?: 'unit';
                    $unitPrice  = $matchedFlower ? $this->unitPrice($matchedFlower) : (float) ($item->price ?? 0);
                    $qty        = (float) ($item->quantity ?? 0);

                    if ($qty > 0) {
                        $this->addFlowerLine($lines, $flowerName, $unit, $qty, $unitPrice, $totalQty, $totalCost);
                    }
                }
            }
        }

        uasort($lines, fn($a, $b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return [
            'lines'      => $lines,
            'total_qty'  => $totalQty,
            'total_cost' => $totalCost,
        ];
    }

    protected function addFlowerLine(
        array &$lines,
        string $flowerName,
        string $unit,
        float $addQty,
        float $unitPrice,
        float &$totalQty,
        float &$totalCost
    ): void {
        $key = $this->norm($flowerName) . '|' . strtolower($unit);

        if (!isset($lines[$key])) {
            $lines[$key] = [
                'flower_name' => $flowerName,
                'unit'        => $unit,
                'unit_price'  => round($unitPrice, 2),
                'qty'         => 0,
                'subtotal'    => 0,
            ];
        }

        $lines[$key]['qty']      += $addQty;
        $lines[$key]['subtotal']  = round($lines[$key]['qty'] * $lines[$key]['unit_price'], 2);
        $totalQty                += $addQty;
        $totalCost               += ($addQty * $lines[$key]['unit_price']);
    }

    protected function unitPrice(FlowerProduct $product): float
    {
        if (!is_null($product->price))         return (float) $product->price;
        if (!is_null($product->per_day_price)) return (float) $product->per_day_price;
        return 0.0;
    }

    protected function norm(?string $s): string
    {
        return Str::of($s ?? '')
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    // ================= NEW: filters + per-user + category-wise =================

    protected function filterSubscriptionsByProduct(
        Collection $subs,
        Collection $flowerByProductId,
        ?string $filterCategory = null,
        ?string $filterProductId = null
    ): Collection {
        return $subs->filter(function ($s) use ($flowerByProductId, $filterCategory, $filterProductId) {
            $p = $flowerByProductId->get($s->product_id);
            if (!$p) return false;

            if ($filterCategory && strcasecmp($p->category ?? '', $filterCategory) !== 0) {
                return false;
            }
            if ($filterProductId && (string)$s->product_id !== (string)$filterProductId) {
                return false;
            }
            return true;
        });
    }

    protected function perUserForDate(
        Carbon $date,
        Collection $flowerByProductId,
        Collection $flowerByNormName,
        ?string $filterCategory = null,
        ?string $filterProductId = null
    ): array {
        // Use the same legacy day window as Day estimate (end_date only)
        $subs = $this->activeSubscriptionsOverlapping($date, $date);

        // Eager load user names for display
        $subs->load(['users:id,userid,name']);

        // Apply product filters
        $subs = $this->filterSubscriptionsByProduct($subs, $flowerByProductId, $filterCategory, $filterProductId);

        // Exclude paused on the specific date + expired
        $subs = $subs->filter(function ($s) use ($date) {
            $inWindow = Carbon::parse($s->start_date)->startOfDay()->lte($date)
                   && Carbon::parse($s->end_date)->endOfDay()->gte($date);

            $paused = false;
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                       && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
            }

            $expired = (isset($s->status) && strtolower((string)$s->status) === 'expired');

            return $inWindow && !$paused && !$expired;
        });

        $rows = [];
        $totals = ['qty'=>0.0,'amount'=>0.0];

        foreach ($subs as $sub) {
            $userName = optional($sub->users)->name;
            $product  = $flowerByProductId->get($sub->product_id);

            if ($product && strtolower((string)$product->category) === 'flower') {
                // Direct flower: +1 unit
                $unitPrice = $this->unitPrice($product);
                $qty = 1.0;
                $rows[] = [
                    'user'        => $userName,
                    'product_id'  => $sub->product_id,
                    'product'     => $product->name,
                    'category'    => $product->category,
                    'unit'        => 'unit',
                    'qty'         => $qty,
                    'unit_price'  => round($unitPrice, 2),
                    'subtotal'    => round($qty * $unitPrice, 2),
                ];
                $totals['qty']    += $qty;
                $totals['amount'] += $qty * $unitPrice;
            } else {
                // Package expansion
                $items = PackageItem::where('product_id', $sub->product_id)->get();
                foreach ($items as $item) {
                    $norm = $this->norm($item->item_name);
                    $matchedFlower = $flowerByNormName->get($norm);

                    $flowerName = $matchedFlower ? $matchedFlower->name : $item->item_name;
                    $category   = $matchedFlower ? ($matchedFlower->category ?? 'flower') : 'unknown';
                    $unit       = $item->unit ?: 'unit';
                    $unitPrice  = $matchedFlower ? $this->unitPrice($matchedFlower) : (float) ($item->price ?? 0);
                    $qty        = (float) ($item->quantity ?? 0);

                    if ($qty > 0) {
                        $rows[] = [
                            'user'        => $userName,
                            'product_id'  => $sub->product_id,
                            'product'     => $flowerName,
                            'category'    => $category,
                            'unit'        => $unit,
                            'qty'         => $qty,
                            'unit_price'  => round($unitPrice, 2),
                            'subtotal'    => round($qty * $unitPrice, 2),
                        ];
                        $totals['qty']    += $qty;
                        $totals['amount'] += $qty * $unitPrice;
                    }
                }
            }
        }

        // Sort by user then product
        usort($rows, function ($a, $b) {
            return strcasecmp($a['user'] ?? '', $b['user'] ?? '') ?: strcasecmp($a['product'], $b['product']);
        });

        return ['rows'=>$rows,'totals'=>$totals];
    }

    protected function categoryWiseForDate(
        Carbon $date,
        Collection $flowerByProductId,
        Collection $flowerByNormName,
        ?string $filterCategory = null,
        ?string $filterProductId = null
    ): array {
        $perUser = $this->perUserForDate($date, $flowerByProductId, $flowerByNormName, $filterCategory, $filterProductId);

        $byCat = [];
        foreach ($perUser['rows'] as $r) {
            $cat = $r['category'] ?: 'unknown';
            if (!isset($byCat[$cat])) {
                $byCat[$cat] = ['qty'=>0.0,'amount'=>0.0];
            }
            $byCat[$cat]['qty']    += $r['qty'];
            $byCat[$cat]['amount'] += $r['subtotal'];
        }

        ksort($byCat, SORT_NATURAL|SORT_FLAG_CASE);
        return $byCat;
    }

    /**
     * CSV export: Tomorrow, Day, and Month sections (kept as-is).
     * (Link passes the filters so you can extend later if desired.)
     */
    public function exportCsv(Request $request)
    {
        $dateStr  = $request->input('date', Carbon::today()->toDateString());
        $monthStr = $request->input('month', Carbon::today()->format('Y-m'));

        // keep filters to preserve in link (not used inside yet)
        $filterCategory  = $request->input('product_category');
        $filterProductId = $request->input('subscription_product_id');

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();
        $tomorrow   = Carbon::tomorrow()->startOfDay();

        $allFlowers = FlowerProduct::select('product_id','name','category','price','mrp','per_day_price','discount','status')->get();
        $flowerByProductId = $allFlowers->keyBy('product_id');
        $flowerByNormName  = $allFlowers->keyBy(fn($f) => $this->norm($f->name));

        $tomorrowEstimate = $this->estimateForTomorrow($tomorrow, $flowerByProductId, $flowerByNormName);
        $dayEstimate      = $this->estimateForDate($date, $flowerByProductId, $flowerByNormName);
        $monthEstimate    = $this->estimateForRange($monthStart, $monthEnd, $flowerByProductId, $flowerByNormName);

        $filename = "flower_estimates_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($tomorrow, $tomorrowEstimate, $dayEstimate, $date, $monthStart, $monthEstimate) {
            $out = fopen('php://output', 'w');

            // Tomorrow
            fputcsv($out, ["Tomorrow-wise Estimate", $tomorrow->toDateString(), "Uses COALESCE(new_date, end_date), excludes paused & expired"]);
            fputcsv($out, ['Flower','Unit','Qty','Unit Price','Subtotal']);
            foreach ($tomorrowEstimate['lines'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price'],$row['subtotal']]);
            }
            fputcsv($out, ['Totals','','',$tomorrowEstimate['total_qty'],$tomorrowEstimate['total_cost']]);
            fputcsv($out, []);

            // Day
            fputcsv($out, ["Day-wise Estimate", $date->toDateString()]);
            fputcsv($out, ['Flower','Unit','Qty','Unit Price','Subtotal']);
            foreach ($dayEstimate['lines'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price'],$row['subtotal']]);
            }
            fputcsv($out, ['Totals','','',$dayEstimate['total_qty'],$dayEstimate['total_cost']]);
            fputcsv($out, []);

            // Month
            fputcsv($out, ["Month-wise Estimate", $monthStart->format('Y-m')]);
            fputcsv($out, ['Flower','Unit','Total Qty','Unit Price','Subtotal']);
            foreach ($monthEstimate['by_flower'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price'],$row['subtotal']]);
            }
            fputcsv($out, ['Month Totals','','',$monthEstimate['total_qty'],$monthEstimate['total_cost']]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
