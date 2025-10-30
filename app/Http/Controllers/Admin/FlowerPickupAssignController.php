<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerProduct;
use App\Models\FlowerVendor;
use App\Models\FlowerDetails;
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
        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : Carbon::tomorrow()->startOfDay();

        // Lookups
        $vendors = FlowerVendor::select('vendor_id','vendor_name')->orderBy('vendor_name')->get();
        $riders  = RiderDetails::select('rider_id','rider_name')->orderBy('rider_name')->get();
        $flowers = FlowerProduct::select('product_id','name')->orderBy('name')->get();
        $units   = PoojaUnit::select('id','unit_name')->get();

        // name -> product_id
        $flowerNameToId = $flowers->pluck('product_id','name')->toArray();

        // normalize unit names → canonical symbols and build map symbol -> unit_id
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $key = $this->normalizeUnitKey($u->unit_name); // returns 'kg','g','l','ml','pcs'
            if ($key) $unitSymbolToId[$key] = $u->id;
        }

        // ====== Tomorrow estimate to prefill estimate quantities =================
        $subs     = $this->fetchActiveSubsEffectiveOn($date);
        $estimate = $this->buildEstimateForSubsOnDate($subs, $date);
        $totals   = array_values($estimate['totals_by_item'] ?? []);

        // ====== Live price index (FlowerDetails) for JS auto-pricing =============
        // Build a product_id → {fd_unit_symbol, fd_unit_id, fd_price} map
        $fdIndexByName = FlowerDetails::query()
            ->select(['name','unit','price'])
            ->where('status', 'active')
            ->get()
            ->keyBy(function ($fd) { return strtolower(trim((string)$fd->name)); });

        // product_id → pricing
        $fdProductPricing = [];
        foreach ($flowers as $f) {
            $nameKey = strtolower(trim((string)$f->name));
            $fd = $fdIndexByName->get($nameKey);
            if ($fd) {
                $sym = $this->normalizeUnitKey($fd->unit); // 'kg','g','l','ml','pcs'
                $fdProductPricing[$f->product_id] = [
                    'fd_unit_symbol' => $sym,
                    'fd_unit_id'     => $unitSymbolToId[$sym] ?? null, // may be null if no matching unit in PoojaUnit
                    'fd_price'       => (float) $fd->price,             // price per FD unit
                ];
            } else {
                // If not found, default to pcs @ 0
                $fdProductPricing[$f->product_id] = [
                    'fd_unit_symbol' => 'pcs',
                    'fd_unit_id'     => $unitSymbolToId['pcs'] ?? null,
                    'fd_price'       => 0.0,
                ];
            }
        }

        // ====== Prefill rows (estimate qty; estimate unit mirrors actual → handled on JS) ====
        $prefillRows = [];
        foreach ($totals as $row) {
            $name     = trim($row['item_name'] ?? '');
            $flowerId = $flowerNameToId[$name] ?? null;

            // Estimate quantity and a displayed unit label (for info only)
            $dispUnit = strtolower((string)($row['total_unit_disp'] ?? ''));
            $prefillRows[] = [
                'flower_id'    => $flowerId,

                // ESTIMATE (prefill qty only; est_unit mirrors actual unit via JS; price is auto)
                'est_quantity' => $row['total_qty_disp'] ?? null,

                // ACTUAL defaults empty
                'unit_id'      => null,
                'quantity'     => null,
                'price'        => null,

                // UX hints
                'flower_name'  => $name,
                'unit_label'   => $dispUnit,
            ];
        }

        // Build a unitId → canonical symbol map for JS conversion
        $unitIdToSymbol = [];
        foreach ($units as $u) {
            $unitIdToSymbol[$u->id] = $this->normalizeUnitKey($u->unit_name); // 'kg','g','l','ml','pcs'
        }

        return view('admin.reports.create-from-estimate', [
            'prefillDate'        => $date->toDateString(),
            'vendors'            => $vendors,
            'riders'             => $riders,
            'flowers'            => $flowers,
            'units'              => $units,
            'prefillRows'        => $prefillRows,
            'todayDate'          => Carbon::today()->toDateString(),
            // for JS auto-pricing
            'fdProductPricing'   => $fdProductPricing,
            'unitIdToSymbol'     => $unitIdToSymbol,
        ]);
    }

    public function saveFlowerPickupAssignRider(Request $request)
    {
        $request->validate([
            'vendor_id'     => 'required|exists:flower__vendor_details,vendor_id',
            'pickup_date'   => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:pickup_date',
            'rider_id'      => 'required|exists:flower__rider_details,rider_id',

            'flower_id'     => 'required|array',
            'flower_id.*'   => 'required|exists:flower_products,product_id',

            // ESTIMATE (all optional as a set, but if provided must be valid)
            'est_unit_id'     => 'sometimes|array',
            'est_unit_id.*'   => 'nullable|exists:pooja_units,id',
            'est_quantity'    => 'sometimes|array',
            'est_quantity.*'  => 'nullable|numeric|min:0.01',
            'est_price'       => 'sometimes|array',
            'est_price.*'     => 'nullable|numeric|min:0',

            // ACTUAL (optional; you may require later if needed)
            'unit_id'       => 'sometimes|array',
            'unit_id.*'     => 'nullable|exists:pooja_units,id',
            'quantity'      => 'sometimes|array',
            'quantity.*'    => 'nullable|numeric|min:0.01',
            'price'         => 'sometimes|array',
            'price.*'       => 'nullable|numeric|min:0',
        ]);

        $pickUpId = 'PICKUP-' . strtoupper(uniqid());

        $pickup = FlowerPickupDetails::create([
            'pick_up_id'     => $pickUpId,
            'vendor_id'      => $request->vendor_id,
            'pickup_date'    => $request->pickup_date,
            'delivery_date'  => $request->delivery_date,
            'rider_id'       => $request->rider_id,
            'total_price'    => 0,
            'payment_method' => null,
            'payment_status' => 'pending',
            'status'         => 'pending',
            'payment_id'     => null,
        ]);

        $flowerIds  = $request->input('flower_id', []);
        $estUnits   = $request->input('est_unit_id', []);
        $estQtys    = $request->input('est_quantity', []);
        $estPrices  = $request->input('est_price', []);

        $unitIds    = $request->input('unit_id',   []);
        $qtys       = $request->input('quantity',  []);
        $prices     = $request->input('price',     []);

        $totalPrice = 0;

        foreach ($flowerIds as $i => $flowerId) {
            $estUnit   = $estUnits[$i]   ?? null;
            $estQty    = isset($estQtys[$i])   ? (float)$estQtys[$i]   : null;
            $estPrice  = isset($estPrices[$i]) ? (float)$estPrices[$i] : null;

            $unitId    = $unitIds[$i]    ?? null;
            $quantity  = isset($qtys[$i])   ? (float)$qtys[$i]   : null;
            $price     = isset($prices[$i]) ? (float)$prices[$i] : null;

            $itemTotal = ($price !== null && $quantity !== null) ? ($price * $quantity) : null;

            FlowerPickupItems::create([
                'pick_up_id'        => $pickUpId,
                'flower_id'         => $flowerId,

                // ESTIMATE
                'est_unit_id'       => $estUnit,
                'est_quantity'      => $estQty,
                'est_price'         => $estPrice,

                // ACTUAL
                'unit_id'           => $unitId,
                'quantity'          => $quantity ?? 0,
                'price'             => $price,

                // actual line total for convenience
                'item_total_price'  => $itemTotal,
            ]);

            if ($itemTotal !== null) {
                $totalPrice += $itemTotal;
            }
        }

        $pickup->update(['total_price' => $totalPrice]);

        return redirect()
            ->back()
            ->with('success', 'Flower pickup details saved successfully!');
    }
        
    public function store(Request $request)
    {
        $request->validate([
            'pickup_date'    => 'required|date',
            'delivery_date'  => 'required|date|after_or_equal:pickup_date',

            'flower_id'      => 'required|array',
            'flower_id.*'    => 'nullable|exists:flower_products,product_id',

            // ESTIMATE
            'est_unit_id'     => 'sometimes|array',
            'est_unit_id.*'   => 'nullable|exists:pooja_units,id',
            'est_quantity'    => 'sometimes|array',
            'est_quantity.*'  => 'nullable|numeric|min:0.01',
            'est_price'       => 'sometimes|array',
            'est_price.*'     => 'nullable|numeric|min:0',

            // ACTUAL
            'unit_id'        => 'required|array',
            'unit_id.*'      => 'nullable|exists:pooja_units,id',

            'quantity'       => 'required|array',
            'quantity.*'     => 'nullable|numeric|min:0.01',

            'price'          => 'sometimes|array',
            'price.*'        => 'nullable|numeric|min:0',

            'row_vendor_id'   => 'required|array',
            'row_vendor_id.*' => 'required|exists:flower__vendor_details,vendor_id',

            'row_rider_id'    => 'sometimes|array',
            'row_rider_id.*'  => 'nullable|exists:flower__rider_details,rider_id',

            'apply_one_rider' => 'sometimes|in:1',
            'bulk_rider_id'   => 'sometimes|nullable|exists:flower__rider_details,rider_id',
        ]);

        $flowerIds   = $request->input('flower_id', []);
        $estUnits    = $request->input('est_unit_id', []);
        $estQtys     = $request->input('est_quantity', []);
        $estPrices   = $request->input('est_price', []);

        $unitIds     = $request->input('unit_id',   []);
        $qtys        = $request->input('quantity',  []);
        $prices      = $request->input('price',     []);
        $rowVendors  = $request->input('row_vendor_id', []);
        $rowRiders   = $request->input('row_rider_id',  []);

        $useBulkRider = $request->boolean('apply_one_rider');
        $bulkRiderId  = $request->input('bulk_rider_id');

        if ($useBulkRider && empty($bulkRiderId)) {
            return back()->withErrors([
                'bulk_rider_id' => 'Please choose a rider to apply to all items.',
            ])->withInput();
        }

        $rows = [];
        foreach ($flowerIds as $i => $fid) {
            // estimate
            $eUnit = $estUnits[$i]    ?? null;
            $eQty  = $estQtys[$i]     ?? null;
            $ePr   = $estPrices[$i]   ?? null;

            // actual
            $unit   = $unitIds[$i]    ?? null;
            $qty    = $qtys[$i]       ?? null;
            $price  = $prices[$i]     ?? null;
            $vendor = $rowVendors[$i] ?? null;
            $rider  = $rowRiders[$i]  ?? null;

            if ($useBulkRider && $bulkRiderId) {
                $rider = $bulkRiderId;
            }

            // keep meaningful rows: require unit (actual) & either flower or qty
            if (($fid || $qty) && $unit) {
                $rows[] = [
                    'flower_id' => $fid ?: null,

                    'est_unit_id'  => $eUnit ?: null,
                    'est_quantity' => $eQty  !== null ? (float)$eQty  : null,
                    'est_price'    => $ePr   !== null ? (float)$ePr   : null,

                    'unit_id'   => $unit ?: null,
                    'quantity'  => $qty   !== null ? (float)$qty   : null,
                    'price'     => $price !== null ? (float)$price : null,

                    'vendor_id' => $vendor,
                    'rider_id'  => $rider,
                ];
            }
        }

        if (empty($rows)) {
            return back()->withErrors([
                'flower_id.0' => 'Please add at least one item row.',
            ])->withInput();
        }

        // group by vendor
        $groups = [];
        foreach ($rows as $r) {
            $groups[$r['vendor_id']][] = $r;
        }

        // ensure at least one rider per vendor group
        foreach ($groups as $vendorId => $items) {
            $hasAnyRider = false;
            foreach ($items as $it) {
                if (!empty($it['rider_id'])) { $hasAnyRider = true; break; }
            }
            if (!$hasAnyRider) {
                return back()->withErrors([
                    "row_rider_id.0" => "Vendor $vendorId: please assign at least one rider (or use bulk rider).",
                ])->withInput();
            }
        }

        return DB::transaction(function () use ($request, $groups) {

            foreach ($groups as $vendorId => $items) {
                // pick header rider (first non-null)
                $nonNullRiders = array_values(array_filter(array_unique(array_map(
                    fn($r) => $r['rider_id'] ?: null, $items
                )), fn($v) => !is_null($v)));
                $headerRiderId = $nonNullRiders[0] ?? null;

                $pickUpId = 'PICKUP-' . strtoupper(uniqid());

                /** @var \App\Models\FlowerPickupDetails $pickup */
                $pickup = FlowerPickupDetails::create([
                    'pick_up_id'     => $pickUpId,
                    'vendor_id'      => $vendorId,
                    'pickup_date'    => $request->pickup_date,
                    'delivery_date'  => $request->delivery_date,
                    'rider_id'       => $headerRiderId,
                    'total_price'    => 0,
                    'payment_method' => null,
                    'payment_status' => 'pending',
                    'status'         => 'pending',
                    'payment_id'     => null,
                ]);

                $groupTotal = 0.0;

                foreach ($items as $r) {
                    $itemTotal = ($r['price'] !== null && $r['quantity'] !== null)
                        ? ($r['price'] * $r['quantity'])
                        : null;

                    FlowerPickupItems::create([
                        'pick_up_id'        => $pickUpId,
                        'flower_id'         => $r['flower_id'],

                        // ESTIMATE
                        'est_unit_id'       => $r['est_unit_id'],
                        'est_quantity'      => $r['est_quantity'],
                        'est_price'         => $r['est_price'],

                        // ACTUAL
                        'unit_id'           => $r['unit_id'],
                        'quantity'          => $r['quantity'] ?? 0,
                        'price'             => $r['price'],

                        // convenience (actual)
                        'item_total_price'  => $itemTotal,
                        // Store per-item vendor/rider if you wish (optional columns):
                        // 'vendor_id'      => $vendorId,
                        // 'rider_id'       => $r['rider_id'],
                    ]);

                    if ($itemTotal !== null) {
                        $groupTotal += $itemTotal;
                    }
                }

                $pickup->update(['total_price' => $groupTotal]);
            }

            return redirect()
                ->route('admin.manageflowerpickupdetails')
                ->with('success', 'Flower pickups saved vendor-wise successfully!');
        });
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
