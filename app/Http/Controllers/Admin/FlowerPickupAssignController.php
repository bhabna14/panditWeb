<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerProduct;
use App\Models\FlowerVendor;
use App\Models\PoojaUnit;
use App\Models\RiderDetails;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlowerPickupAssignController extends Controller
{
    public function createFromEstimate(Request $request)
    {
        // which date to prefill from
        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : Carbon::tomorrow()->startOfDay();

        // lookups
        $vendors = FlowerVendor::select('vendor_id','vendor_name')->orderBy('vendor_name')->get();
        $riders  = RiderDetails::select('rider_id','rider_name')->orderBy('rider_name')->get();
        $flowers = FlowerProduct::select('product_id','name')->orderBy('name')->get();
        $units   = PoojaUnit::select('id','unit_name')->get();

        // name -> product_id
        $flowerNameToId = $flowers->pluck('product_id','name')->toArray();

        // normalize unit names to symbols we use in estimate (kg,g,l,ml,pcs)
        // build a symbol -> unit_id map based on your unit_name values
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $key = $this->normalizeUnitKey($u->unit_name);
            if ($key) $unitSymbolToId[$key] = $u->id;
        }

        // compute "tomorrow estimate" (items + totals_by_item) for this date
        $subs = $this->fetchActiveSubsEffectiveOn($date);
        $estimate = $this->buildEstimateForSubsOnDate($subs, $date);
        $totals = array_values($estimate['totals_by_item'] ?? []);

        // server-side prefill rows from totals_by_item
        $prefillRows = [];
        foreach ($totals as $row) {
            $name = trim($row['item_name'] ?? '');
            $flowerId = $flowerNameToId[$name] ?? null;

            // map display unit to our unit_id
            $dispUnit = strtolower((string)($row['total_unit_disp'] ?? ''));
            $unitKey  = $this->normalizeUnitKey($dispUnit);
            $unitId   = $unitSymbolToId[$unitKey] ?? null;

            $prefillRows[] = [
                'flower_id' => $flowerId,
                'unit_id'   => $unitId,
                'quantity'  => $row['total_qty_disp'] ?? null,
                'flower_name' => $name,      // for UX if not matched
                'unit_label'  => $dispUnit,  // for UX if not matched
            ];
        }

        return view('admin.reports.create-from-estimate', [
            'prefillDate'     => $date->toDateString(),
            'vendors'         => $vendors,
            'riders'          => $riders,
            'flowers'         => $flowers,
            'units'           => $units,
            'prefillRows'     => $prefillRows,
        ]);
    }

   public function store(Request $request)
{
    // -------- Validate -------------------------------------------------------
    $request->validate([
        'pickup_date'    => 'required|date',
        'delivery_date'  => 'required|date|after_or_equal:pickup_date',

        // core item arrays
        'flower_id'      => 'required|array',
        'flower_id.*'    => 'nullable|exists:flower_products,product_id',

        'unit_id'        => 'required|array',
        'unit_id.*'      => 'nullable|exists:pooja_units,id',

        'quantity'       => 'required|array',
        'quantity.*'     => 'nullable|numeric|min:0.01',

        'price'          => 'sometimes|array',
        'price.*'        => 'nullable|numeric|min:0',

        // item-wise vendor & rider (nullable each row)
        'row_vendor_id'   => 'sometimes|array',
        'row_vendor_id.*' => 'nullable|exists:flower__vendor_details,vendor_id',

        'row_rider_id'    => 'sometimes|array',
        'row_rider_id.*'  => 'nullable|exists:flower__rider_details,rider_id',

        // bulk rider toggle + value
        'apply_one_rider' => 'sometimes|in:1',
        'bulk_rider_id'   => 'sometimes|nullable|exists:flower__rider_details,rider_id',
    ]);

    // -------- Read inputs safely --------------------------------------------
    $flowerIds  = $request->input('flower_id', []);
    $unitIds    = $request->input('unit_id',   []);
    $qtys       = $request->input('quantity',  []);
    $prices     = $request->input('price',     []); // may be missing entirely

    $rowVendors = $request->input('row_vendor_id', []);
    $rowRiders  = $request->input('row_rider_id',  []);

    $useBulkRider = $request->boolean('apply_one_rider');
    $bulkRiderId  = $request->input('bulk_rider_id');

    // -------- Build normalized rows (keep only meaningful) -------------------
    $rows = [];
    foreach ($flowerIds as $i => $fid) {
        $unit = $unitIds[$i]  ?? null;
        $qty  = $qtys[$i]     ?? null;
        $prc  = $prices[$i]   ?? null;

        $vendorId = $rowVendors[$i] ?? null;
        $riderId  = $rowRiders[$i]  ?? null;

        // Apply bulk rider on the server too (robust against JS being bypassed)
        if ($useBulkRider && $bulkRiderId) {
            $riderId = $bulkRiderId;
        }

        // Keep only rows that have a unit and either a flower or quantity
        if (($fid || $qty) && $unit) {
            $rows[] = [
                'flower_id' => $fid ?: null,
                'unit_id'   => $unit ?: null,
                'quantity'  => $qty  !== null ? (float) $qty  : null,
                'price'     => $prc  !== null ? (float) $prc  : null,   // nullable
                'vendor_id' => $vendorId ?: null,                        // item-wise
                'rider_id'  => $riderId  ?: null,                        // item-wise
            ];
        }
    }

    if (empty($rows)) {
        return back()
            ->withErrors(['flower_id.0' => 'Please add at least one item row.'])
            ->withInput();
    }

    // -------- Determine header-level vendor/rider (only if uniform) ---------
    $allVendors = array_values(array_unique(array_map(fn($r) => $r['vendor_id'] ?: null, $rows)));
    $allRiders  = array_values(array_unique(array_map(fn($r) => $r['rider_id']  ?: null, $rows)));

    $headerVendorId = (count($allVendors) === 1) ? $allVendors[0] : null;
    $headerRiderId  = (count($allRiders)  === 1) ? $allRiders[0]  : null;

    // -------- Create header (pickup) ----------------------------------------
    $pickUpId = 'PICKUP-' . strtoupper(uniqid());

    /** @var \App\Models\FlowerPickupDetails $pickup */
    $pickup = \App\Models\FlowerPickupDetails::create([
        'pick_up_id'     => $pickUpId,
        'vendor_id'      => $headerVendorId,         // nullable if mixed item vendors
        'pickup_date'    => $request->pickup_date,
        'delivery_date'  => $request->delivery_date,
        'rider_id'       => $headerRiderId,          // nullable if mixed item riders
        'total_price'    => 0,
        'payment_method' => null,
        'payment_status' => 'pending',
        'status'         => 'pending',
        'payment_id'     => null,
    ]);

    // -------- Create items + compute total ----------------------------------
    $totalPrice = 0.0;

    foreach ($rows as $r) {
        \App\Models\FlowerPickupItems::create([
            'pick_up_id' => $pickUpId,
            'flower_id'  => $r['flower_id'],
            'unit_id'    => $r['unit_id'],
            'quantity'   => $r['quantity'] ?? 0,
            'price'      => $r['price'],          // nullable
            'vendor_id'  => $r['vendor_id'],      // <-- ensure column exists in table
            'rider_id'   => $r['rider_id'],       // <-- ensure column exists in table
        ]);

        if ($r['price'] !== null && $r['quantity'] !== null) {
            $totalPrice += $r['price'] * $r['quantity'];
        }
    }

    $pickup->update(['total_price' => $totalPrice]);

    return redirect()
        ->route('admin.manageflowerpickupdetails')
        ->with('success', 'Flower pickup details saved successfully!');
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
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $date->toDateString())
            ->get();

        // exclude paused on this date
        return $subs->filter(function ($s) use ($date) {
            if ($s->pause_start_date && $s->pause_end_date) {
                $paused = Carbon::parse($s->pause_start_date)->startOfDay()->lte($date)
                    && Carbon::parse($s->pause_end_date)->endOfDay()->gte($date);
                if ($paused) return false;
            }
            return true;
        })->values();
    }

    private function buildEstimateForSubsOnDate($subs, Carbon $date): array
    {
        $byProduct = $subs->groupBy('product_id');
        $dayTotalsByItemBase = [];
        $productsForDay = [];
        $grand = 0.0;

        foreach ($byProduct as $productId => $subsForProduct) {
            $product   = optional($subsForProduct->first())->flowerProducts;
            $subsCount = $subsForProduct->count();
            $items     = [];
            $productTotal = 0.0;

            if ($product) {
                foreach ($product->packageItems as $pi) {
                    $perItemQty      = (float) ($pi->quantity ?? 0);
                    $origUnit        = strtolower(trim($pi->unit ?? ''));
                    $itemPricePerSub = (float) ($pi->price ?? 0);

                    $category     = $this->inferCategory($origUnit);
                    if ($category === 'unknown') { $category = 'count'; $origUnit = 'pcs'; }
                    $toBaseFactor = $this->toBaseFactor($origUnit);

                    $totalQtyBase = $perItemQty * $subsCount * $toBaseFactor;
                    [$qtyDisp, $unitDisp] = $this->formatQtyByCategoryFromBase($totalQtyBase, $category);

                    $totalPrice = $itemPricePerSub * $subsCount;

                    $items[] = [
                        'item_name'         => $pi->item_name,
                        'category'          => $category,
                        'per_item_qty'      => $perItemQty,
                        'per_item_unit'     => $origUnit,
                        'item_price_per_sub'=> $itemPricePerSub,
                        'total_qty_base'    => $totalQtyBase,
                        'total_qty_disp'    => $qtyDisp,
                        'total_unit_disp'   => $unitDisp,
                        'total_price'       => $totalPrice,
                    ];

                    $productTotal += $totalPrice;

                    $key = strtolower($pi->item_name).'|'.$category;
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

            $grand += $productTotal;
            $productsForDay[$productId] = [
                'product'              => $product,
                'subs_count'           => $subsCount,
                'items'                => $items,
                'product_total'        => $productTotal,
                'bundle_total_per_sub' => array_sum(array_column($items, 'item_price_per_sub')),
            ];
        }

        return [
            'date'               => $date->toDateString(),
            'products'           => $productsForDay,
            'grand_total_amount' => $grand,
            'totals_by_item'     => $this->formatTotalsByItem($dayTotalsByItemBase),
        ];
    }

    private function inferCategory(string $unit): string
    {
        $u = strtolower(trim($unit));
        if (in_array($u, ['g','gm','gram','grams','kg','kgs','kilogram','kilograms'])) return 'weight';
        if (in_array($u, ['ml','milliliter','milliliters','l','lt','liter','litre','liters','litres'])) return 'volume';
        if (in_array($u, ['piece','pieces','pc','pcs','count'])) return 'count';
        return 'unknown';
    }

    private function toBaseFactor(string $unit): float
    {
        $u = strtolower(trim($unit));
        return match ($u) {
            'g','gm','gram','grams' => 1.0,
            'kg','kgs','kilogram','kilograms' => 1000.0,
            'ml','milliliter','milliliters' => 1.0,
            'l','lt','liter','litre','liters','litres' => 1000.0,
            'piece','pieces','pc','pcs','count' => 1.0,
            default => 1.0,
        };
    }

    private function formatQtyByCategoryFromBase(float $qtyBase, string $category): array
    {
        return match ($category) {
            'weight' => $qtyBase >= 1000 ? [round($qtyBase/1000,3),'kg'] : [round($qtyBase,3),'g'],
            'volume' => $qtyBase >= 1000 ? [round($qtyBase/1000,3),'L']  : [round($qtyBase,3),'ml'],
            default  => [round($qtyBase,0),'pcs'],
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
        uasort($rows, fn($a,$b) => strcasecmp($a['item_name'], $b['item_name']));
        return $rows;
    }

    /** Normalize varied unit strings to standard keys: kg,g,l,ml,pcs */
    private function normalizeUnitKey(?string $v): ?string
    {
        $u = strtolower(trim((string)$v));
        return match ($u) {
            'kg','kgs','kilogram','kilograms' => 'kg',
            'g','gm','gram','grams'           => 'g',
            'l','lt','liter','litre','liters','litres' => 'l',
            'ml','milliliter','milliliters'   => 'ml',
            'pcs','pc','piece','pieces','count' => 'pcs',
            default => null
        };
    }
}
