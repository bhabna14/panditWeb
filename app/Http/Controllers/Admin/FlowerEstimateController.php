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

        // Day estimate (legacy window rules: end_date only)
        $dayEstimate = $this->estimateForDate($date, $flowerByProductId, $flowerByNormName);

        // Month estimate (legacy window rules: end_date only)
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $flowerByProductId, $flowerByNormName);

        // Tomorrow estimate (COALESCE(new_date, end_date), excludes expired)
        $tomorrowEstimate = $this->estimateForTomorrow($tomorrow, $flowerByProductId, $flowerByNormName);

        return view('admin.reports.flower-estimates', [
            'date'              => $date,
            'monthStart'        => $monthStart,
            'dayEstimate'       => $dayEstimate,
            'monthEstimate'     => $monthEstimate,
            'tomorrow'          => $tomorrow,
            'tomorrowEstimate'  => $tomorrowEstimate,
            'selectedDate'      => $date->toDateString(),
            'selectedMonth'     => $monthStart->format('Y-m'),
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

        // Aggregate month by flower/unit with WEIGHTED average unit price
        $byFlower = [];
        $totalQty = 0.0;
        $totalCost = 0.0;

        foreach ($perDay as $data) {
            foreach ($data['lines'] as $key => $line) {
                if (!isset($byFlower[$key])) {
                    $byFlower[$key] = [
                        'flower_name' => $line['flower_name'],
                        'unit'        => $line['unit'],
                        'unit_price'  => 0.0,         // will be weighted avg
                        'qty'         => 0.0,
                        'total_value' => 0.0,         // sum(qty*price)
                    ];
                }
                $byFlower[$key]['qty']         += $line['qty'];
                $byFlower[$key]['total_value'] += $line['total_value'];

                // recompute weighted average unit price for display
                $byFlower[$key]['unit_price'] = $byFlower[$key]['qty'] > 0
                    ? round($byFlower[$key]['total_value'] / $byFlower[$key]['qty'], 2)
                    : 0.0;

                $totalQty  += $line['qty'];
                $totalCost += $line['total_value'];
            }
        }

        uasort($byFlower, fn($a,$b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return [
            'per_day'    => $perDay,
            'by_flower'  => $byFlower,
            'total_qty'  => $totalQty,
            'total_cost' => round($totalCost, 2),
        ];
    }

    /**
     * Active subscriptions overlapping the range (end_date only).
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
     * Tomorrow-specific: effective end = COALESCE(new_date, end_date), exclude expired.
     */
    protected function activeSubscriptionsOverlappingEffective(Carbon $start, Carbon $end)
    {
        return Subscription::query()
            ->where(function ($q) {
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
     * - Direct flower product => +1 unit per day per subscription.
     * - Package product => expand PackageItem rows and add their quantities.
     * Returns lines with: qty, unit_price (weighted avg), total_value, etc.
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

            $expired = (isset($s->status) && strtolower((string)$s->status) === 'expired');

            return $inWindow && !$paused && !$expired;
        });

        if ($deliveries->isEmpty()) {
            return [
                'lines'      => [],
                'total_qty'  => 0.0,
                'total_cost' => 0.0,
            ];
        }

        // Preload package items for involved product_ids
        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $packageItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $lines = [];
        $totalQty  = 0.0;
        $totalCost = 0.0;

        foreach ($deliveries as $sub) {
            $product = $flowerByProductId->get($sub->product_id);

            if ($product && strtolower((string) $product->category) === 'flower') {
                // Direct flower: +1 unit per day per subscription
                $this->addFlowerLine($lines, $product->name, 'unit', 1.0, $this->unitPrice($product), $totalQty, $totalCost);
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

        // sort by flower name
        uasort($lines, fn($a, $b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return [
            'lines'      => $lines,
            'total_qty'  => $totalQty,
            'total_cost' => round($totalCost, 2),
        ];
    }

    /**
     * Aggregation helper using WEIGHTED average pricing.
     */
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
                'qty'         => 0.0,
                'unit_price'  => 0.0,  // will be weighted avg for display
                'total_value' => 0.0,  // running sum of qty*price (for accuracy)
            ];
        }

        // Update running totals for this flower/unit
        $lines[$key]['qty']         += $addQty;
        $lines[$key]['total_value'] += ($addQty * $unitPrice);

        // Weighted average unit price for display
        $lines[$key]['unit_price'] = $lines[$key]['qty'] > 0
            ? round($lines[$key]['total_value'] / $lines[$key]['qty'], 2)
            : 0.0;

        // Global totals
        $totalQty  += $addQty;
        $totalCost += ($addQty * $unitPrice);
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

    /**
     * CSV export (no per-line subtotal; totals use accurate total_value sums).
     */
    public function exportCsv(Request $request)
    {
        $dateStr  = $request->input('date', Carbon::today()->toDateString());
        $monthStr = $request->input('month', Carbon::today()->format('Y-m'));

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
            fputcsv($out, ["Tomorrow-wise Estimate", $tomorrow->toDateString(), "Uses COALESCE(new_date, end_date)."]);
            fputcsv($out, ['Flower','Unit','Qty','Avg Unit Price']);
            foreach ($tomorrowEstimate['lines'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price']]);
            }
            fputcsv($out, ['Totals','', $tomorrowEstimate['total_qty'], 'Total ₹ '.$tomorrowEstimate['total_cost']]);
            fputcsv($out, []);

            // Day
            fputcsv($out, ["Day-wise Estimate", $date->toDateString()]);
            fputcsv($out, ['Flower','Unit','Qty','Avg Unit Price']);
            foreach ($dayEstimate['lines'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price']]);
            }
            fputcsv($out, ['Totals','', $dayEstimate['total_qty'], 'Total ₹ '.$dayEstimate['total_cost']]);
            fputcsv($out, []);

            // Month
            fputcsv($out, ["Month-wise Estimate", $monthStart->format('Y-m')]);
            fputcsv($out, ['Flower','Unit','Total Qty','Avg Unit Price']);
            foreach ($monthEstimate['by_flower'] as $row) {
                fputcsv($out, [$row['flower_name'],$row['unit'],$row['qty'],$row['unit_price']]);
            }
            fputcsv($out, ['Month Totals','', $monthEstimate['total_qty'], 'Total ₹ '.$monthEstimate['total_cost']]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
