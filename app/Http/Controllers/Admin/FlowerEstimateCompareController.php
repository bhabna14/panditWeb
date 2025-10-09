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
use App\Models\FlowerVendor;

class FlowerVendorCompareController extends Controller
{
    public function index(Request $request)
    {
        // Inputs
        $dateStr    = $request->input('date', Carbon::today()->toDateString());
        $monthStr   = $request->input('month', Carbon::today()->format('Y-m'));

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Load flowers (for estimate unit price fallback)
        $allFlowers = FlowerProduct::select('product_id','name','category','price','per_day_price','status')->get();
        $flowerById = $allFlowers->keyBy('product_id');
        $flowerByNormName = $allFlowers->keyBy(fn($f) => $this->norm($f->name));

        // ---- DAY SECTION ----
        $estDay   = $this->estimateTotalsForDate($date, $flowerById, $flowerByNormName);
        $actDay   = $this->actualTotalsPerVendorForDate($date);

        $compareDay = $this->composeVendorCompare($actDay['per_vendor'], $estDay['total_qty'], $estDay['total_value']);

        // ---- MONTH SECTION ----
        $estMonth = $this->estimateTotalsForRange($monthStart, $monthEnd, $flowerById, $flowerByNormName);
        $actMonth = $this->actualTotalsPerVendorForRange($monthStart, $monthEnd);

        $compareMonth = $this->composeVendorCompare($actMonth['per_vendor'], $estMonth['total_qty'], $estMonth['total_value']);

        // Vendors for friendly names in case some vendor didn’t pick in the window
        $vendors = FlowerVendor::select('vendor_id','vendor_name')->orderBy('vendor_name')->get()->keyBy('vendor_id');

        return view('admin.reports.vendor-compare', [
            'date'          => $date,
            'monthStart'    => $monthStart,
            'selectedDate'  => $date->toDateString(),
            'selectedMonth' => $monthStart->format('Y-m'),

            'vendors'       => $vendors,

            'estDay'        => $estDay,
            'compareDay'    => $compareDay,

            'estMonth'      => $estMonth,
            'compareMonth'  => $compareMonth,
        ]);
    }

    // ========== BUILD COMPARISON ROWS ==========

    protected function composeVendorCompare(array $actualPerVendor, float $estQty, float $estValue): array
    {
        $rows = [];
        $sumActQty = 0.0;
        $sumActVal = 0.0;
        foreach ($actualPerVendor as $vendorId => $v) {
            $actQty = (float) $v['qty'];
            $actVal = (float) $v['value'];
            $rows[] = [
                'vendor_id'   => $vendorId,
                'vendor_name' => $v['vendor_name'],
                'act_qty'     => $actQty,
                'act_value'   => $actVal,
                'est_qty'     => $estQty,
                'est_value'   => $estValue,
                'diff_qty'    => $actQty - $estQty,
                'diff_value'  => $actVal - $estValue,
            ];
            $sumActQty += $actQty;
            $sumActVal += $actVal;
        }

        // Overall totals row (All Vendors)
        $totals = [
            'act_qty'    => $sumActQty,
            'act_value'  => $sumActVal,
            'est_qty'    => $estQty,
            'est_value'  => $estValue,
            'diff_qty'   => $sumActQty - $estQty,
            'diff_value' => $sumActVal - $estValue,
        ];

        // Sort by vendor name
        usort($rows, fn($a,$b) => strcasecmp($a['vendor_name'], $b['vendor_name']));

        return ['rows' => $rows, 'totals' => $totals];
    }

    // ========== ESTIMATE: TOTALS ONLY (QTY & VALUE) ==========

    protected function estimateTotalsForDate(
        Carbon $date,
        Collection $flowerById,
        Collection $flowerByNormName
    ): array {
        $subs = $this->activeSubscriptionsOverlappingEffective($date, $date);

        $productIds = $subs->pluck('product_id')->unique()->all();
        if (empty($productIds)) {
            return ['total_qty' => 0.0, 'total_value' => 0.0];
        }

        $pkgItemsByProduct = PackageItem::whereIn('product_id', $productIds)->get()->groupBy('product_id');

        $totalQty = 0.0;
        $totalVal = 0.0;

        foreach ($subs->groupBy('product_id') as $pid => $subsForProduct) {
            $countSubs = $subsForProduct->count();
            $pkgItems  = $pkgItemsByProduct->get($pid) ?? collect();

            foreach ($pkgItems as $it) {
                $qty  = (float) ($it->quantity ?? 0);
                $unit = $it->unit ?: 'unit';

                // derive per-unit price based on flower product if name matches, else bundle-price/qty
                $matched = $flowerByNormName->get($this->norm($it->item_name));
                $unitPrice = $matched ? $this->unitPrice($matched)
                                      : ($qty > 0 ? (float)$it->price / $qty : 0.0);

                $addQty = $qty * $countSubs;
                $totalQty += $addQty;
                $totalVal += ($addQty * $unitPrice);
            }
        }

        return ['total_qty' => round($totalQty, 2), 'total_value' => round($totalVal, 2)];
    }

    protected function estimateTotalsForRange(
        Carbon $start,
        Carbon $end,
        Collection $flowerById,
        Collection $flowerByNormName
    ): array {
        $cursor = $start->copy();
        $sumQty = 0.0;
        $sumVal = 0.0;

        while ($cursor->lte($end)) {
            $d = $this->estimateTotalsForDate($cursor, $flowerById, $flowerByNormName);
            $sumQty += $d['total_qty'];
            $sumVal += $d['total_value'];
            $cursor->addDay();
        }
        return ['total_qty' => round($sumQty, 2), 'total_value' => round($sumVal, 2)];
    }

    protected function unitPrice(FlowerProduct $p): float
    {
        if (!is_null($p->price))         return (float)$p->price;
        if (!is_null($p->per_day_price)) return (float)$p->per_day_price;
        return 0.0;
    }

    // ========== ACTUAL PICKUPS: GROUPED BY VENDOR (QTY & VALUE) ==========

    protected function actualTotalsPerVendorForDate(Carbon $date): array
    {
        $rows = FlowerPickupDetails::with(['flowerPickupItems'])
            ->whereDate('pickup_date', '=', $date->toDateString())
            ->get();

        return $this->sumByVendor($rows);
    }

    protected function actualTotalsPerVendorForRange(Carbon $start, Carbon $end): array
    {
        $rows = FlowerPickupDetails::with(['flowerPickupItems'])
            ->whereDate('pickup_date', '>=', $start->toDateString())
            ->whereDate('pickup_date', '<=', $end->toDateString())
            ->get();

        return $this->sumByVendor($rows);
    }

    protected function sumByVendor(Collection $pickupDetails): array
    {
        $perVendor = []; // vendor_id => ['vendor_name'=>..., 'qty'=>..., 'value'=>...]
        foreach ($pickupDetails as $detail) {
            $vid  = $detail->vendor_id ?? 0;
            $vname= $detail->vendor_name ?? ($detail->vendor->vendor_name ?? 'Unknown');

            if (!isset($perVendor[$vid])) {
                $perVendor[$vid] = ['vendor_name' => $vname, 'qty' => 0.0, 'value' => 0.0];
            }

            foreach ($detail->flowerPickupItems as $it) {
                $q = (float)($it->quantity ?? 0);
                $p = (float)($it->price ?? 0); // ASSUME unit price
                $perVendor[$vid]['qty']   += $q;
                $perVendor[$vid]['value'] += ($q * $p);
            }
        }

        // Round
        foreach ($perVendor as &$pv) {
            $pv['qty']   = round($pv['qty'], 2);
            $pv['value'] = round($pv['value'], 2);
        }

        return ['per_vendor' => $perVendor];
    }

    // ========== SUBS QUERY HELPERS ==========

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

    protected function norm(?string $s): string
    {
        return Str::of($s ?? '')
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    // ========== CSV ==========

    public function exportCsv(Request $request)
    {
        $dateStr    = $request->input('date', Carbon::today()->toDateString());
        $monthStr   = $request->input('month', Carbon::today()->format('Y-m'));

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        $allFlowers = FlowerProduct::select('product_id','name','category','price','per_day_price','status')->get();
        $flowerById = $allFlowers->keyBy('product_id');
        $flowerByNormName = $allFlowers->keyBy(fn($f) => $this->norm($f->name));

        $estDay     = $this->estimateTotalsForDate($date, $flowerById, $flowerByNormName);
        $actDay     = $this->actualTotalsPerVendorForDate($date);
        $cmpDay     = $this->composeVendorCompare($actDay['per_vendor'], $estDay['total_qty'], $estDay['total_value']);

        $estMonth   = $this->estimateTotalsForRange($monthStart, $monthEnd, $flowerById, $flowerByNormName);
        $actMonth   = $this->actualTotalsPerVendorForRange($monthStart, $monthEnd);
        $cmpMonth   = $this->composeVendorCompare($actMonth['per_vendor'], $estMonth['total_qty'], $estMonth['total_value']);

        $filename = "vendor_compare_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($date, $cmpDay, $monthStart, $cmpMonth) {
            $out = fopen('php://output', 'w');

            $write = function($title, $subtitle, $cmp) use ($out) {
                fputcsv($out, [$title, $subtitle]);
                fputcsv($out, ['Vendor','Actual Qty','Actual Value','Est Qty','Est Value','Δ Qty','Δ Value']);
                foreach ($cmp['rows'] as $r) {
                    fputcsv($out, [
                        $r['vendor_name'],
                        $r['act_qty'],
                        $r['act_value'],
                        $r['est_qty'],
                        $r['est_value'],
                        $r['diff_qty'],
                        $r['diff_value'],
                    ]);
                }
                $t = $cmp['totals'];
                fputcsv($out, ['All Vendors',
                    $t['act_qty'], $t['act_value'],
                    $t['est_qty'], $t['est_value'],
                    $t['diff_qty'], $t['diff_value'],
                ]);
                fputcsv($out, []);
            };

            $write('Day', $date->toDateString(), $cmpDay);
            $write('Month', $monthStart->format('Y-m'), $cmpMonth);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
