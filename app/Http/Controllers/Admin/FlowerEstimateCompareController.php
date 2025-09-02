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
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerVendor; // for vendor dropdown (if you have it)

class FlowerEstimateCompareController extends Controller
{
    public function index(Request $request)
    {
        // Inputs (defaults)
        $dateStr    = $request->input('date', Carbon::today()->toDateString());
        $monthStr   = $request->input('month', Carbon::today()->format('Y-m'));
        $vendorId   = $request->input('vendor_id');            // optional
        $userId     = $request->input('user_id');              // optional filter for estimates
        $date       = Carbon::parse($dateStr)->startOfDay();
        $tomorrow   = Carbon::tomorrow()->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Preload flower products and lookup maps
        $allFlowers = FlowerProduct::select('product_id','name','category','price','per_day_price','status')->get();
        $flowerById = $allFlowers->keyBy('product_id');
        $flowerByNormName = $allFlowers->keyBy(function ($f) { return $this->norm($f->name); });

        // --- Build Comparisons ---
        $compareTomorrow = $this->compareForDate($tomorrow, $flowerById, $flowerByNormName, $userId, $vendorId, true);  // effective end-date
        $compareToday    = $this->compareForDate($date,     $flowerById, $flowerByNormName, $userId, $vendorId, true);
        $compareMonth    = $this->compareForRange($monthStart, $monthEnd, $flowerById, $flowerByNormName, $userId, $vendorId);

        // Vendors list (optional dropdown)
        $vendors = FlowerVendor::select('vendor_id','vendor_name')->orderBy('vendor_name')->get();

        return view('admin.reports.flower-compare', [
            'date'          => $date,
            'tomorrow'      => $tomorrow,
            'monthStart'    => $monthStart,
            'vendors'       => $vendors,
            'vendorId'      => $vendorId,
            'userId'        => $userId,
            'selectedDate'  => $date->toDateString(),
            'selectedMonth' => $monthStart->format('Y-m'),

            'compareTomorrow'=> $compareTomorrow,
            'compareToday'   => $compareToday,
            'compareMonth'   => $compareMonth,
        ]);
    }

    // ---------- Core compare builders ----------

    protected function compareForDate(
        Carbon $date,
        Collection $flowerById,
        Collection $flowerByNormName,
        ?int $userId = null,
        ?int $vendorId = null,
        bool $effectiveEnd = true
    ): array {
        $est = $this->estimateForDate($date, $flowerById, $flowerByNormName, $userId, $effectiveEnd);
        $act = $this->pickupsForDate($date, $vendorId);

        return $this->mergeEstimateActual($est['lines'], $act['lines'], [
            'est_total_qty'   => $est['total_qty'],
            'est_total_value' => $est['total_cost'],
            'act_total_qty'   => $act['total_qty'],
            'act_total_value' => $act['total_value'],
        ]);
    }

    protected function compareForRange(
        Carbon $start,
        Carbon $end,
        Collection $flowerById,
        Collection $flowerByNormName,
        ?int $userId = null,
        ?int $vendorId = null
    ): array {
        $est = $this->estimateForRange($start, $end, $flowerById, $flowerByNormName, $userId);
        $act = $this->pickupsForRange($start, $end, $vendorId);

        // Convert estimate 'by_flower' format into lines keyed by flower|unit
        $estLines = [];
        foreach ($est['by_flower'] as $row) {
            $key = $this->norm($row['flower_name']) . '|' . strtolower($row['unit'] ?? 'unit');
            $estLines[$key] = [
                'flower_name' => $row['flower_name'],
                'unit'        => $row['unit'] ?? 'unit',
                'qty'         => (float) $row['qty'],
                'unit_price'  => (float) $row['unit_price'],
                'subtotal'    => (float) $row['subtotal'],
            ];
        }

        return $this->mergeEstimateActual($estLines, $act['lines'], [
            'est_total_qty'   => $est['total_qty'],
            'est_total_value' => $est['total_cost'],
            'act_total_qty'   => $act['total_qty'],
            'act_total_value' => $act['total_value'],
        ]);
    }

    protected function mergeEstimateActual(array $estLines, array $actLines, array $totals): array
    {
        $keys = array_unique(array_merge(array_keys($estLines), array_keys($actLines)));
        $rows = [];

        $sumDiffQty = 0.0;
        $sumDiffVal = 0.0;

        foreach ($keys as $key) {
            $e = $estLines[$key] ?? null;
            $a = $actLines[$key] ?? null;

            $flowerName = $e['flower_name'] ?? ($a['flower_name'] ?? '—');
            $unit       = $e['unit'] ?? ($a['unit'] ?? 'unit');

            $estQty   = $e['qty']        ?? 0.0;
            $estValue = $e['subtotal']   ?? 0.0;

            $actQty   = $a['qty']        ?? 0.0;
            $actValue = $a['subtotal']   ?? 0.0;

            $diffQty  = $actQty - $estQty;
            $diffVal  = $actValue - $estValue;

            $sumDiffQty += $diffQty;
            $sumDiffVal += $diffVal;

            $rows[] = [
                'flower_name' => $flowerName,
                'unit'        => $unit,
                'est_qty'     => $estQty,
                'act_qty'     => $actQty,
                'diff_qty'    => $diffQty,
                'est_value'   => $estValue,
                'act_value'   => $actValue,
                'diff_value'  => $diffVal,
            ];
        }

        // sort by name
        usort($rows, fn($x, $y) => strcasecmp($x['flower_name'], $y['flower_name']));

        return [
            'rows' => $rows,
            'totals' => [
                'est_qty'    => (float) ($totals['est_total_qty']   ?? 0),
                'act_qty'    => (float) ($totals['act_total_qty']   ?? 0),
                'diff_qty'   => (float) ($sumDiffQty),
                'est_value'  => (float) ($totals['est_total_value'] ?? 0),
                'act_value'  => (float) ($totals['act_total_value'] ?? 0),
                'diff_value' => (float) ($sumDiffVal),
            ],
        ];
    }

    // ---------- Estimates ----------

    protected function estimateForDate(
        Carbon $date,
        Collection $flowerById,
        Collection $flowerByNormName,
        ?int $userId = null,
        bool $effectiveEnd = true // true => use COALESCE(new_date, end_date)
    ): array {
        $subs = $effectiveEnd
            ? $this->activeSubscriptionsOverlappingEffective($date, $date, $userId)
            : $this->activeSubscriptionsOverlapping($date, $date, $userId);

        return $this->tallyForSubscriptionsOnDate($subs, $date, $flowerById, $flowerByNormName);
    }

    protected function estimateForRange(
        Carbon $start,
        Carbon $end,
        Collection $flowerById,
        Collection $flowerByNormName,
        ?int $userId = null
    ): array {
        $subs = $this->activeSubscriptionsOverlappingEffective($start, $end, $userId);

        $perDay = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $perDay[$cursor->toDateString()] = $this->tallyForSubscriptionsOnDate($subs, $cursor, $flowerById, $flowerByNormName);
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

    protected function tallyForSubscriptionsOnDate(
        Collection $subscriptions,
        Carbon $date,
        Collection $flowerById,
        Collection $flowerByNormName
    ): array {
        $deliveries = $subscriptions->filter(function ($s) use ($date) {
            $endEff = $s->new_date ?? $s->end_date;
            $inWindow = Carbon::parse($s->start_date)->startOfDay()->lte($date)
                      && Carbon::parse($endEff)->endOfDay()->gte($date);

            $paused = false;
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                       && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
            }

            $expired = (isset($s->status) && strtolower((string)$s->status) === 'expired');

            return $inWindow && !$paused && !$expired;
        });

        if ($deliveries->isEmpty()) {
            return ['lines' => [], 'total_qty' => 0, 'total_cost' => 0];
        }

        $productIds = $deliveries->pluck('product_id')->unique()->all();
        $packageItemsByProduct = PackageItem::whereIn('product_id', $productIds)->get()->groupBy('product_id');

        $lines = [];
        $totalQty = 0;
        $totalCost = 0;

        foreach ($deliveries as $sub) {
            $product = $flowerById->get($sub->product_id);

            if ($product && strtolower((string)$product->category) === 'flower') {
                $this->addLine($lines, $product->name, 'unit', 1, $this->unitPrice($product), $totalQty, $totalCost);
            } else {
                $items = $packageItemsByProduct->get($sub->product_id) ?? collect();
                foreach ($items as $item) {
                    $norm = $this->norm($item->item_name);
                    $matched = $flowerByNormName->get($norm);

                    $flowerName = $matched ? $matched->name : $item->item_name;
                    $unit       = $item->unit ?: 'unit';
                    $unitPrice  = $matched ? $this->unitPrice($matched) : (float) ($item->price ?? 0);
                    $qty        = (float) ($item->quantity ?? 0);

                    if ($qty > 0) {
                        $this->addLine($lines, $flowerName, $unit, $qty, $unitPrice, $totalQty, $totalCost);
                    }
                }
            }
        }

        uasort($lines, fn($a, $b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return ['lines' => $lines, 'total_qty' => $totalQty, 'total_cost' => $totalCost];
    }

    protected function unitPrice(FlowerProduct $product): float
    {
        if (!is_null($product->price))         return (float) $product->price;
        if (!is_null($product->per_day_price)) return (float) $product->per_day_price;
        return 0.0;
    }

    protected function addLine(
        array &$lines,
        string $flowerName,
        string $unit,
        float $addQty,
        float $unitPrice,
        float &$totalQty,
        float &$totalCost
    ): void {
        $key = $this->norm($flowerName) . '|' . strtolower($unit ?: 'unit');

        if (!isset($lines[$key])) {
            $lines[$key] = [
                'flower_name' => $flowerName,
                'unit'        => $unit ?: 'unit',
                'unit_price'  => round($unitPrice, 2),
                'qty'         => 0.0,
                'subtotal'    => 0.0,
            ];
        }

        $lines[$key]['qty']      += $addQty;
        $lines[$key]['subtotal']  = round($lines[$key]['qty'] * $lines[$key]['unit_price'], 2);
        $totalQty                += $addQty;
        $totalCost               += ($addQty * $lines[$key]['unit_price']);
    }

    // ---------- Subscriptions: overlap helpers ----------

    protected function activeSubscriptionsOverlappingEffective(Carbon $start, Carbon $end, ?int $userId = null)
    {
        return Subscription::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
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

    protected function activeSubscriptionsOverlapping(Carbon $start, Carbon $end, ?int $userId = null)
    {
        return Subscription::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get([
                'subscription_id','order_id','user_id','product_id',
                'start_date','end_date',
                'pause_start_date','pause_end_date',
                'status','is_active'
            ]);
    }

    // ---------- Actual pickups ----------

    protected function pickupsForDate(Carbon $date, ?int $vendorId = null): array
    {
        $rows = FlowerPickupDetails::with([
                'flowerPickupItems.flower',
                'flowerPickupItems.unit',
                'vendor',
            ])
            ->whereDate('pickup_date', '=', $date->toDateString())
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->get();

        return $this->aggregatePickupRows($rows);
    }

    protected function pickupsForRange(Carbon $start, Carbon $end, ?int $vendorId = null): array
    {
        $rows = FlowerPickupDetails::with([
                'flowerPickupItems.flower',
                'flowerPickupItems.unit',
                'vendor',
            ])
            ->whereDate('pickup_date', '>=', $start->toDateString())
            ->whereDate('pickup_date', '<=', $end->toDateString())
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->get();

        return $this->aggregatePickupRows($rows);
    }

    protected function aggregatePickupRows(Collection $pickupDetails): array
    {
        $lines = [];
        $totalQty = 0.0;
        $totalValue = 0.0;

        foreach ($pickupDetails as $detail) {
            foreach ($detail->flowerPickupItems as $it) {
                $name = $it->flower?->name ?? 'Unknown';
                $unit = $it->unit?->unit_name ?? 'unit';
                $qty  = (float) ($it->quantity ?? 0);
                $price = (float) ($it->price ?? 0); // ASSUMPTION: unit price; adjust if it's total

                $key = $this->norm($name) . '|' . strtolower($unit);
                if (!isset($lines[$key])) {
                    $lines[$key] = [
                        'flower_name' => $name,
                        'unit'        => $unit,
                        'qty'         => 0.0,
                        'unit_price'  => $price, // last-seen; not used for subtotal calc
                        'subtotal'    => 0.0,
                    ];
                }

                $lines[$key]['qty']      += $qty;
                $lines[$key]['subtotal'] += ($qty * $price);

                $totalQty   += $qty;
                $totalValue += ($qty * $price);
            }
        }

        // round subtotals
        foreach ($lines as &$L) {
            $L['subtotal'] = round($L['subtotal'], 2);
        }

        // sort by name
        uasort($lines, fn($a,$b) => strcasecmp($a['flower_name'], $b['flower_name']));

        return [
            'lines'       => $lines,
            'total_qty'   => $totalQty,
            'total_value' => $totalValue,
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

    // ---------- CSV Export (optional) ----------

    public function exportCsv(Request $request)
    {
        $dateStr    = $request->input('date', Carbon::today()->toDateString());
        $monthStr   = $request->input('month', Carbon::today()->format('Y-m'));
        $vendorId   = $request->input('vendor_id');
        $userId     = $request->input('user_id');

        $date       = Carbon::parse($dateStr)->startOfDay();
        $tomorrow   = Carbon::tomorrow()->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        $allFlowers = FlowerProduct::select('product_id','name','category','price','per_day_price','status')->get();
        $flowerById = $allFlowers->keyBy('product_id');
        $flowerByNorm= $allFlowers->keyBy(fn($f) => $this->norm($f->name));

        $cmpTomorrow = $this->compareForDate($tomorrow, $flowerById, $flowerByNorm, $userId, $vendorId, true);
        $cmpToday    = $this->compareForDate($date,     $flowerById, $flowerByNorm, $userId, $vendorId, true);
        $cmpMonth    = $this->compareForRange($monthStart, $monthEnd, $flowerById, $flowerByNorm, $userId, $vendorId);

        $filename = "flower_compare_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($tomorrow, $cmpTomorrow, $date, $cmpToday, $monthStart, $cmpMonth) {
            $out = fopen('php://output', 'w');

            // Helper
            $writeSection = function($title, $subTitle, $data) use ($out) {
                fputcsv($out, [$title, $subTitle]);
                fputcsv($out, ['Flower','Unit','Est Qty','Act Qty','Diff Qty','Est Value','Act Value','Diff Value']);
                foreach ($data['rows'] as $r) {
                    fputcsv($out, [
                        $r['flower_name'],
                        $r['unit'],
                        $r['est_qty'],
                        $r['act_qty'],
                        $r['diff_qty'],
                        $r['est_value'],
                        $r['act_value'],
                        $r['diff_value'],
                    ]);
                }
                $t = $data['totals'];
                fputcsv($out, ['Totals','',
                    $t['est_qty'], $t['act_qty'], $t['diff_qty'],
                    $t['est_value'], $t['act_value'], $t['diff_value']
                ]);
                fputcsv($out, []); // blank line
            };

            $writeSection('Tomorrow Compare', $tomorrow->toDateString(), $cmpTomorrow);
            $writeSection('Today Compare', $date->toDateString(), $cmpToday);
            $writeSection('Month Compare', $monthStart->format('Y-m'), $cmpMonth);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
