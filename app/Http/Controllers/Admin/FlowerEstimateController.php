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

class FlowerEstimateController extends Controller
{
    public function index(Request $request)
    {
        // Inputs (defaults to "today" and "this month")
        $dateStr  = $request->input('date', Carbon::today()->toDateString());
        $monthStr = $request->input('month', Carbon::today()->format('Y-m'));

        $date = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Preload flower products and fast lookup maps
        $allFlowers = FlowerProduct::select(
            'product_id','name','category','price','mrp','per_day_price','discount','status'
        )->get();

        $flowerByProductId = $allFlowers->keyBy('product_id');
        $flowerByNormName  = $allFlowers->keyBy(function ($f) {
            return $this->norm($f->name);
        });

        // Day estimate
        $dayEstimate = $this->estimateForDate($date, $flowerByProductId, $flowerByNormName);

        // Month estimate
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $flowerByProductId, $flowerByNormName);

        return view('admin.reports.flower-estimates', [
            'date'           => $date,
            'monthStart'     => $monthStart,
            'dayEstimate'    => $dayEstimate,
            'monthEstimate'  => $monthEstimate,
            'selectedDate'   => $date->toDateString(),
            'selectedMonth'  => $monthStart->format('Y-m'),
        ]);
    }

    /**
     * Estimate for a single date.
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
     * Estimate for a date range (e.g., full month).
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

        // Aggregate month totals by flower
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
     * Active subscriptions (status 'active' or is_active=1) overlapping the range.
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
                'status','is_active'
            ]);
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
                $this->addFlowerLine(
                    $lines,
                    $product->name,
                    'unit',
                    1,
                    $this->unitPrice($product),
                    $totalQty,
                    $totalCost
                );
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
                        $this->addFlowerLine(
                            $lines,
                            $flowerName,
                            $unit,
                            $qty,
                            $unitPrice,
                            $totalQty,
                            $totalCost
                        );
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

    /**
     * Optional CSV export of current selection (day + month).
     */
    public function exportCsv(Request $request)
    {
        $dateStr  = $request->input('date', Carbon::today()->toDateString());
        $monthStr = $request->input('month', Carbon::today()->format('Y-m'));

        $date = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        $allFlowers = FlowerProduct::select('product_id','name','category','price','mrp','per_day_price','discount','status')->get();
        $flowerByProductId = $allFlowers->keyBy('product_id');
        $flowerByNormName  = $allFlowers->keyBy(fn($f) => $this->norm($f->name));

        $dayEstimate   = $this->estimateForDate($date, $flowerByProductId, $flowerByNormName);
        $monthEstimate = $this->estimateForRange($monthStart, $monthEnd, $flowerByProductId, $flowerByNormName);

        $filename = "flower_estimates_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($dayEstimate, $monthEstimate, $date, $monthStart) {
            $out = fopen('php://output', 'w');

            // Day section
            fputcsv($out, ["Day-wise Estimate", $date->toDateString()]);
            fputcsv($out, ['Flower','Unit','Qty','Unit Price','Subtotal']);
            foreach ($dayEstimate['lines'] as $row) {
                fputcsv($out, [
                    $row['flower_name'],
                    $row['unit'],
                    $row['qty'],
                    $row['unit_price'],
                    $row['subtotal'],
                ]);
            }
            fputcsv($out, ['Totals','','',$dayEstimate['total_qty'],$dayEstimate['total_cost']]);
            fputcsv($out, []); // blank line

            // Month section
            fputcsv($out, ["Month-wise Estimate", $monthStart->format('Y-m')]);
            fputcsv($out, ['Flower','Unit','Total Qty','Unit Price','Subtotal']);
            foreach ($monthEstimate['by_flower'] as $row) {
                fputcsv($out, [
                    $row['flower_name'],
                    $row['unit'],
                    $row['qty'],
                    $row['unit_price'],
                    $row['subtotal'],
                ]);
            }
            fputcsv($out, ['Month Totals','','',$monthEstimate['total_qty'],$monthEstimate['total_cost']]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
