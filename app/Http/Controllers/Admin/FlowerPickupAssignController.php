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
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $key = $this->normalizeUnitKey($u->unit_name);
            if ($key) $unitSymbolToId[$key] = $u->id;
        }

        // compute "tomorrow estimate" (items + totals_by_item) for this date
        $subs     = $this->fetchActiveSubsEffectiveOn($date);
        $estimate = $this->buildEstimateForSubsOnDate($subs, $date);
        $totals   = array_values($estimate['totals_by_item'] ?? []);

        // server-side prefill rows from totals_by_item
        $prefillRows = [];
        foreach ($totals as $row) {
            $name     = trim($row['item_name'] ?? '');
            $flowerId = $flowerNameToId[$name] ?? null;

            $dispUnit = strtolower((string)($row['total_unit_disp'] ?? ''));
            $unitKey  = $this->normalizeUnitKey($dispUnit);
            $unitId   = $unitSymbolToId[$unitKey] ?? null;

            $prefillRows[] = [
                'flower_id'   => $flowerId,
                'unit_id'     => $unitId,
                'quantity'    => $row['total_qty_disp'] ?? null,
                'flower_name' => $name,     // for UX if not matched
                'unit_label'  => $dispUnit, // for UX if not matched
            ];
        }

        return view('admin.reports.create-from-estimate', [
            'prefillDate' => $date->toDateString(),
            'vendors'     => $vendors,
            'riders'      => $riders,
            'flowers'     => $flowers,
            'units'       => $units,
            'prefillRows' => $prefillRows,
            'todayDate'   => Carbon::today()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        // ---------- Validate (vendor required per row for grouping) ------------
        $request->validate([
            'pickup_date'    => 'required|date',
            'delivery_date'  => 'required|date|after_or_equal:pickup_date',

            'flower_id'      => 'required|array',
            'flower_id.*'    => 'nullable|exists:flower_products,product_id',

            'unit_id'        => 'required|array',
            'unit_id.*'      => 'nullable|exists:pooja_units,id',

            'quantity'       => 'required|array',
            'quantity.*'     => 'nullable|numeric|min:0.01',

            'price'          => 'sometimes|array',
            'price.*'        => 'nullable|numeric|min:0',

            'row_vendor_id'   => 'required|array',
            'row_vendor_id.*' => 'required|exists:flower__vendor_details,vendor_id', // <-- vendor must be set per item

            'row_rider_id'    => 'sometimes|array',
            'row_rider_id.*'  => 'nullable|exists:flower__rider_details,rider_id',

            'apply_one_rider' => 'sometimes|in:1',
            'bulk_rider_id'   => 'sometimes|nullable|exists:flower__rider_details,rider_id',
        ]);

        $flowerIds  = $request->input('flower_id', []);
        $unitIds    = $request->input('unit_id',   []);
        $qtys       = $request->input('quantity',  []);
        $prices     = $request->input('price',     []);
        $rowVendors = $request->input('row_vendor_id', []);
        $rowRiders  = $request->input('row_rider_id',  []);

        $useBulkRider = $request->boolean('apply_one_rider');
        $bulkRiderId  = $request->input('bulk_rider_id');

        if ($useBulkRider && empty($bulkRiderId)) {
            return back()->withErrors([
                'bulk_rider_id' => 'Please choose a rider to apply to all items.',
            ])->withInput();
        }

        // ---------- Normalize rows (keep meaningful ones) ----------------------
        $rows = [];
        foreach ($flowerIds as $i => $fid) {
            $unit   = $unitIds[$i]    ?? null;
            $qty    = $qtys[$i]       ?? null;
            $price  = $prices[$i]     ?? null;
            $vendor = $rowVendors[$i] ?? null;
            $rider  = $rowRiders[$i]  ?? null;

            if ($useBulkRider && $bulkRiderId) {
                $rider = $bulkRiderId;
            }

            // needs a unit; and either flower or qty
            if (($fid || $qty) && $unit) {
                $rows[] = [
                    'flower_id' => $fid ?: null,
                    'unit_id'   => $unit ?: null,
                    'quantity'  => $qty   !== null ? (float)$qty   : null,
                    'price'     => $price !== null ? (float)$price : null,
                    'vendor_id' => $vendor, // required by validation
                    'rider_id'  => $rider,  // nullable here
                ];
            }
        }

        if (empty($rows)) {
            return back()->withErrors([
                'flower_id.0' => 'Please add at least one item row.',
            ])->withInput();
        }

        // ---------- Group by vendor -------------------------------------------
        $groups = [];
        foreach ($rows as $r) {
            $groups[$r['vendor_id']][] = $r;
        }

        // Safety: ensure each group has at least one rider (header rider NOT NULL)
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

        // ---------- Persist all vendor headers & items in 1 transaction --------
        return DB::transaction(function () use ($request, $groups) {

            $createdPickups = []; // for optional post-use

            foreach ($groups as $vendorId => $items) {
                // Determine header rider for this vendor group:
                $allRidersInGroup = array_values(array_unique(array_map(
                    fn($r) => $r['rider_id'] ?: null, $items
                )));
                // choose a single common rider if possible, otherwise first non-null
                $nonNullRiders = array_values(array_filter($allRidersInGroup, fn($v) => !is_null($v)));
                $headerRiderId = (count($nonNullRiders) === 1) ? $nonNullRiders[0] : $nonNullRiders[0];

                // Create header for this vendor
                $pickUpId = 'PICKUP-' . strtoupper(uniqid());
                /** @var \App\Models\FlowerPickupDetails $pickup */
                $pickup = FlowerPickupDetails::create([
                    'pick_up_id'     => $pickUpId,
                    'vendor_id'      => $vendorId,                   // NOT NULL (group key)
                    'pickup_date'    => $request->pickup_date,
                    'delivery_date'  => $request->delivery_date,
                    'rider_id'       => $headerRiderId,              // NOT NULL satisfied
                    'total_price'    => 0,
                    'payment_method' => null,
                    'payment_status' => 'pending',
                    'status'         => 'pending',
                    'payment_id'     => null,
                ]);

                $groupTotal = 0.0;

                foreach ($items as $r) {
                    FlowerPickupItems::create([
                        'pick_up_id' => $pickUpId,
                        'flower_id'  => $r['flower_id'],
                        'unit_id'    => $r['unit_id'],
                        'quantity'   => $r['quantity'] ?? 0,
                        'price'      => $r['price'],        // nullable
                        'vendor_id'  => $vendorId,          // store vendor on item too
                        'rider_id'   => $r['rider_id'],     // item rider (may vary)
                    ]);

                    if ($r['price'] !== null && $r['quantity'] !== null) {
                        $groupTotal += $r['price'] * $r['quantity'];
                    }
                }

                $pickup->update(['total_price' => $groupTotal]);
                $createdPickups[] = $pickUpId;
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
