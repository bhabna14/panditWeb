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
use App\Models\RiderDetails;
use App\Models\PoojaUnit;

class FlowerEstimateCompareController extends Controller
{
    public function index(Request $request)
    {
        $preset = $request->string('preset')->toString();
        $mode   = $request->string('mode')->toString() ?: 'day';

        [$start, $end] = $this->resolveRange($request, $preset);
        if ($mode === 'month' && !$request->filled('start_date') && !$request->filled('end_date') && !$preset) {
            $today = Carbon::today();
            $start = $today->copy()->startOfMonth();
            $end   = $today->copy()->endOfMonth();
        }
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // Tomorrow block
        $tomorrow = Carbon::tomorrow()->startOfDay();
        $tomorrowSubs     = $this->fetchActiveSubsEffectiveOn($tomorrow);
        $tomorrowEstimate = $this->buildEstimateForSubsOnDate($tomorrowSubs, $tomorrow);

        // Build daily (and range totals)  --- (use YOUR working code here)
        // ... keep your existing daily/monthly/range code exactly as you have it ...
        // At the end of that logic you return the view; before that, add lookups below.

        // ======= NEW: Lookups for the modal =======
        $vendors = FlowerVendor::select('vendor_id', 'vendor_name')->orderBy('vendor_name')->get();
        $riders  = RiderDetails::select('rider_id', 'rider_name')->orderBy('rider_name')->get();
        $flowers = FlowerProduct::select('product_id', 'name')->orderBy('name')->get();
        // if your PoojaUnit table has a "symbol" (kg, g, L, ml, pcs) keep/select it; else use unit_name as symbol
        $units   = PoojaUnit::select('id', 'unit_name', DB::raw("LOWER(COALESCE(symbol, unit_name)) as symbol"))->get();

        // name → id (exact match) for auto prefill
        $flowerNameToId = $flowers->pluck('product_id', 'name')->toArray();

        // symbol → id
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $unitSymbolToId[strtolower($u->symbol)] = $u->id;
        }

        // ======= Return view with NEW data =======
        return view('admin.reports.flower-estimates', [
            'start'              => $start->toDateString(),
            'end'                => $end->toDateString(),
            'mode'               => $mode,
            'preset'             => $preset,
            'dailyEstimates'     => $dailyEstimates ?? [],
            'monthlyEstimates'   => $monthlyEstimates ?? [],
            'tomorrowDate'       => $tomorrow->toDateString(),
            'tomorrowEstimate'   => $tomorrowEstimate,
            'rangeTotals'        => $rangeTotals ?? [
                'by_item' => [], 'by_category' => []
            ],

            // NEW for modal
            'vendors'           => $vendors,
            'riders'            => $riders,
            'flowers'           => $flowers,
            'units'             => $units,
            'flowerNameToId'    => $flowerNameToId,
            'unitSymbolToId'    => $unitSymbolToId,
        ]);
    }
    /**
     * Merge actual-per-vendor with a global estimate (qty/value/unit).
     * Adds per-row act_unit and est_unit and totals act_unit/est_unit.
     */
  protected function composeVendorCompare(array $actualPerVendor, array $estimateTotals): array
{
    $rows = [];
    $sumActQty = 0.0;
    $sumActVal = 0.0;

    $estQty   = (float) ($estimateTotals['total_qty']   ?? 0);
    $estValue = (float) ($estimateTotals['total_value'] ?? 0);
    $estUnit  = (string)($estimateTotals['unit_label']  ?? 'units');

    // pull global unit counts (may not exist)
    $globalUnitCounts = $actualPerVendor['_unit_counts'] ?? [];

    foreach ($actualPerVendor as $vendorId => $v) {
        // skip meta rows or malformed rows
        if (!is_array($v) || !array_key_exists('qty', $v)) {
            continue;
        }

        $actQty  = (float) ($v['qty']   ?? 0);
        $actVal  = (float) ($v['value'] ?? 0);
        $actUnit = (string)($v['unit_label'] ?? 'units');

        $rows[] = [
            'vendor_id'   => $vendorId,
            'vendor_name' => $v['vendor_name'] ?? 'Unknown',
            'act_qty'     => $actQty,
            'act_value'   => $actVal,
            'act_unit'    => $actUnit,
            'est_qty'     => $estQty,
            'est_value'   => $estValue,
            'est_unit'    => $estUnit,
            'diff_qty'    => $actQty - $estQty,
            'diff_value'  => $actVal - $estValue,
        ];

        $sumActQty += $actQty;
        $sumActVal += $actVal;
    }

    $totals = [
        'act_qty'    => $sumActQty,
        'act_value'  => $sumActVal,
        'act_unit'   => $this->pickUnitLabelFromCounts($globalUnitCounts) ?: 'units',
        'est_qty'    => $estQty,
        'est_value'  => $estValue,
        'est_unit'   => $estUnit,
        'diff_qty'   => $sumActQty - $estQty,
        'diff_value' => $sumActVal - $estValue,
    ];

    usort($rows, fn($a,$b) => strcasecmp($a['vendor_name'], $b['vendor_name']));
    return ['rows' => $rows, 'totals' => $totals];
}

protected function sumByVendor(Collection $pickupDetails): array
{
    $perVendor = [];      // vendor_id => ['vendor_name'=>..., 'qty'=>..., 'value'=>..., 'unit_label'=>..., '_unit_counts'=>[]]
    $globalUnitCounts = [];

    foreach ($pickupDetails as $detail) {
        $vid   = $detail->vendor_id ?? 0;
        $vname = $detail->vendor->vendor_name ?? ($detail->vendor_name ?? 'Unknown');

        if (!isset($perVendor[$vid])) {
            $perVendor[$vid] = [
                'vendor_name'  => $vname,
                'qty'          => 0.0,
                'value'        => 0.0,
                'unit_label'   => 'units',
                '_unit_counts' => [],
            ];
        }

        foreach ($detail->flowerPickupItems as $it) {
            $q = (float)($it->quantity ?? 0);
            $p = (float)($it->price ?? 0); // unit price
            $unitName = $this->prettyUnit($it->unit->unit_name ?? ($it->unit ?? ''));

            $perVendor[$vid]['qty']   += $q;
            $perVendor[$vid]['value'] += ($q * $p);

            if ($unitName) {
                $perVendor[$vid]['_unit_counts'][$unitName] = ($perVendor[$vid]['_unit_counts'][$unitName] ?? 0) + $q;
                $globalUnitCounts[$unitName] = ($globalUnitCounts[$unitName] ?? 0) + $q;
            }
        }
    }

    // finalize labels + rounding
    foreach ($perVendor as $vid => &$pv) {
        $pv['qty']        = round($pv['qty'], 2);
        $pv['value']      = round($pv['value'], 2);
        $pv['unit_label'] = $this->pickUnitLabelFromCounts($pv['_unit_counts']) ?: 'units';
        unset($pv['_unit_counts']);
    }
    unset($pv);

    // attach global unit counts as meta for totals (composeVendorCompare will handle it safely)
    $perVendor['_unit_counts'] = $globalUnitCounts;

    return ['per_vendor' => $perVendor];
}
    // ================= ESTIMATES (QTY + TOTAL ₹ + UNIT LABEL) =================

    /**
     * Day estimate:
     * - Overlap: start_date <= D <= COALESCE(new_date,end_date)
     * - exclude paused/expired on D
     * - PRICE = sum(item.price_per_subscription) * (#subs)
     * - QTY   = sum(item.quantity)              * (#subs)
     * - UNIT label = dominant package item unit by quantity; "units" if mixed/none
     */
    protected function estimateTotalsForDate(Carbon $date): array
    {
        $subs = Subscription::query()
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'paused'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $date->toDateString())
            ->get([
                'subscription_id','order_id','user_id','product_id',
                'start_date','end_date','new_date',
                'pause_start_date','pause_end_date','status','is_active'
            ]);

        // Exclude paused/expired on this exact date
        $subs = $subs->filter(function ($s) use ($date) {
            if (isset($s->status) && strtolower((string)$s->status) === 'expired') {
                return false;
            }
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                       && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
                if ($paused) return false;
            }
            return true;
        });

        if ($subs->isEmpty()) {
            return ['total_qty' => 0.0, 'total_value' => 0.0, 'unit_label' => 'units', '_unit_counts' => []];
        }

        $productIds = $subs->pluck('product_id')->unique()->all();
        $pkgItemsByProduct = PackageItem::whereIn('product_id', $productIds)
            ->get(['product_id','item_name','quantity','unit','price'])
            ->groupBy('product_id');

        $totalQty = 0.0;
        $totalVal = 0.0;
        $unitCounts = []; // unit => qty contributed

        foreach ($subs->groupBy('product_id') as $pid => $subsForProduct) {
            $subsCount = $subsForProduct->count();
            $pkgItems  = $pkgItemsByProduct->get($pid) ?? collect();

            foreach ($pkgItems as $it) {
                $qtyPerSub   = (float) ($it->quantity ?? 0);
                $pricePerSub = (float) ($it->price ?? 0);
                $unit        = $this->prettyUnit($it->unit ?? '');

                if ($qtyPerSub > 0) {
                    $add = $qtyPerSub * $subsCount;
                    $totalQty += $add;
                    if ($unit) {
                        $unitCounts[$unit] = ($unitCounts[$unit] ?? 0) + $add;
                    }
                }
                $totalVal += ($pricePerSub * $subsCount);
            }
        }

        return [
            'total_qty'    => round($totalQty, 2),
            'total_value'  => round($totalVal, 2),
            'unit_label'   => $this->pickUnitLabelFromCounts($unitCounts) ?: 'units',
            '_unit_counts' => $unitCounts,
        ];
    }

    protected function estimateTotalsForRange(Carbon $start, Carbon $end): array
    {
        $cursor = $start->copy();
        $sumQty = 0.0;
        $sumVal = 0.0;
        $unitCounts = [];

        while ($cursor->lte($end)) {
            $d = $this->estimateTotalsForDate($cursor);
            $sumQty += $d['total_qty'];
            $sumVal += $d['total_value'];
            foreach (($d['_unit_counts'] ?? []) as $u => $q) {
                $unitCounts[$u] = ($unitCounts[$u] ?? 0) + $q;
            }
            $cursor->addDay();
        }

        return [
            'total_qty'    => round($sumQty, 2),
            'total_value'  => round($sumVal, 2),
            'unit_label'   => $this->pickUnitLabelFromCounts($unitCounts) ?: 'units',
            '_unit_counts' => $unitCounts,
        ];
    }

    // ================= ACTUAL PICKUPS (BY VENDOR) =================

    protected function actualTotalsPerVendorForDate(Carbon $date): array
    {
        $rows = FlowerPickupDetails::with(['flowerPickupItems.flower','flowerPickupItems.unit','vendor'])
            ->whereDate('pickup_date', '=', $date->toDateString())
            ->get();

        return $this->sumByVendor($rows);
    }

    protected function actualTotalsPerVendorForRange(Carbon $start, Carbon $end): array
    {
        $rows = FlowerPickupDetails::with(['flowerPickupItems.flower','flowerPickupItems.unit','vendor'])
            ->whereDate('pickup_date', '>=', $start->toDateString())
            ->whereDate('pickup_date', '<=', $end->toDateString())
            ->get();

        return $this->sumByVendor($rows);
    }

    /**
     * Per-vendor totals with a unit label:
     * - We add quantities and values
     * - Track quantity contribution per unit; pick the dominant unit as the label
     * - Also build global unit counts for footer
     */
   

    // ================= Utils =================

    protected function norm(?string $s): string
    {
        return Str::of($s ?? '')
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    protected function prettyUnit(?string $u): string
    {
        $u = trim((string)$u);
        if ($u === '') return '';
        // normalize a few common variants
        $lu = strtolower($u);
        return match ($lu) {
            'gm','g','gram','grams'                  => 'Gm',
            'kg','kilogram','kilograms'              => 'Kg',
            'ml','milliliter','milliliters'          => 'Ml',
            'l','lt','liter','litre','liters','litres'=> 'L',
            'piece','pieces','pc','pcs','count'      => 'Piece',
            default                                  => ucfirst($lu),
        };
    }

    protected function pickUnitLabelFromCounts(array $counts): ?string
    {
        if (empty($counts)) return null;
        if (count($counts) === 1) return array_key_first($counts);
        // choose the unit with highest contributed qty
        arsort($counts);
        return array_key_first($counts);
    }

    // ================= CSV (keeps labels in headers) =================

    public function exportCsv(Request $request)
    {
        $dateStr    = $request->input('date', Carbon::today()->toDateString());
        $monthStr   = $request->input('month', Carbon::today()->format('Y-m'));

        $date       = Carbon::parse($dateStr)->startOfDay();
        $monthStart = Carbon::parse($monthStr . '-01')->startOfDay();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        $estDay   = $this->estimateTotalsForDate($date);
        $actDay   = $this->actualTotalsPerVendorForDate($date);
        $cmpDay   = $this->composeVendorCompare($actDay['per_vendor'], $estDay);

        $estMonth = $this->estimateTotalsForRange($monthStart, $monthEnd);
        $actMonth = $this->actualTotalsPerVendorForRange($monthStart, $monthEnd);
        $cmpMonth = $this->composeVendorCompare($actMonth['per_vendor'], $estMonth);

        $filename = "vendor_compare_{$date->toDateString()}_{$monthStart->format('Y-m')}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($date, $cmpDay, $monthStart, $cmpMonth) {
            $out = fopen('php://output', 'w');

            $write = function($title, $subtitle, $cmp) use ($out) {
                fputcsv($out, [$title, $subtitle]);
                fputcsv($out, ['Vendor','Actual Qty','Actual Unit','Actual Value','Est Qty','Est Unit','Est Value','Δ Qty','Δ Value']);
                foreach ($cmp['rows'] as $r) {
                    fputcsv($out, [
                        $r['vendor_name'],
                        $r['act_qty'], $r['act_unit'], $r['act_value'],
                        $r['est_qty'], $r['est_unit'], $r['est_value'],
                        $r['diff_qty'], $r['diff_value'],
                    ]);
                }
                $t = $cmp['totals'];
                fputcsv($out, ['All Vendors',
                    $t['act_qty'], $t['act_unit'], $t['act_value'],
                    $t['est_qty'], $t['est_unit'], $t['est_value'],
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
