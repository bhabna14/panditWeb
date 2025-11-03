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
use App\Models\FlowerRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema; 

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

        // canonical unit symbol -> unit_id
        $unitSymbolToId = [];
        foreach ($units as $u) {
            $key = $this->normalizeUnitKey($u->unit_name); // 'kg','g','l','ml','pcs'
            if ($key) $unitSymbolToId[$key] = $u->id;
        }

        // ===== Live price index (for client-side preview) ==========================
        $fdIndexByName = FlowerDetails::query()
            ->select(['name','unit','price'])
            ->where('status', 'active')
            ->get()
            ->keyBy(fn($fd) => strtolower(trim((string)$fd->name)));

        $fdProductPricing = [];
        foreach ($flowers as $f) {
            $nameKey = strtolower(trim((string)$f->name));
            $fd = $fdIndexByName->get($nameKey);
            if ($fd) {
                $sym = $this->normalizeUnitKey($fd->unit);
                $fdProductPricing[$f->product_id] = [
                    'fd_unit_symbol' => $sym,
                    'fd_unit_id'     => $unitSymbolToId[$sym] ?? null,
                    'fd_price'       => (float) $fd->price,
                ];
            } else {
                $fdProductPricing[$f->product_id] = [
                    'fd_unit_symbol' => 'pcs',
                    'fd_unit_id'     => $unitSymbolToId['pcs'] ?? null,
                    'fd_price'       => 0.0,
                ];
            }
        }

        $prefillRows = [];

        // ==========================================================================
        // 1) FLOWER REQUESTS (tomorrow) — aggregate flower-wise + unit-wise
        // ==========================================================================
        $requests = FlowerRequest::with('flowerRequestItems')
            ->whereDate('date', $date->toDateString())
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get();

        $requestAgg = []; // key: name|sym
        foreach ($requests as $req) {
            foreach ($req->flowerRequestItems ?? [] as $ri) {
                $name = trim((string)($ri->item_name ?? ''));
                if ($name === '') continue;

                $sym = $this->normalizeUnitKey((string)($ri->unit ?? 'pcs'));
                $key = strtolower($name).'|'.$sym;

                $qty   = (float)($ri->quantity ?? 0);
                $price = isset($ri->price) ? (float)$ri->price : null;

                if (!isset($requestAgg[$key])) {
                    $requestAgg[$key] = ['name'=>$name,'sym'=>$sym,'qty'=>0.0,'price'=>$price];
                }
                $requestAgg[$key]['qty'] += $qty;
                if ($price !== null) $requestAgg[$key]['price'] = $price;
            }
        }

        foreach ($requestAgg as $agg) {
            $name   = $agg['name'];
            $sym    = $agg['sym'];
            $qty    = $agg['qty'];
            $price  = $agg['price'];

            $prefillRows[] = [
                'flower_id'    => $flowerNameToId[$name] ?? null,
                'est_quantity' => $qty,
                'unit_id'      => $unitSymbolToId[$sym] ?? null,
                'quantity'     => $qty,
                'price'        => $price,
                'flower_name'  => $name,
                'unit_label'   => $sym,
                'source'       => 'request',
            ];
        }

        // ==========================================================================
        // 2) CUSTOMIZE ORDERS (tomorrow) — aggregate flower-wise + unit-wise
        //    Works with either \App\Models\CustomizeOrder or \App\Models\UserCustomizeOrder
        // ==========================================================================
        $customizeAgg = []; // key: name|sym

        $coModel = null;
        if (class_exists(\App\Models\CustomizeOrder::class)) {
            $coModel = \App\Models\CustomizeOrder::class;
        } elseif (class_exists(\App\Models\UserCustomizeOrder::class)) {
            $coModel = \App\Models\UserCustomizeOrder::class;
        }

        if ($coModel) {
            // try common relation names
            $orders = $coModel::query()
                ->when(true, function ($q) use ($date) {
                    // flexible date field (date or delivery_date)
                    $q->whereDate('date', $date->toDateString())
                    ->orWhereDate('delivery_date', $date->toDateString());
                })
                ->when(schema_has_column((new $coModel)->getTable(), 'status'), function($q){
                    // if status exists, exclude cancelled/rejected if present
                    $q->whereNotIn('status', ['cancelled','rejected']);
                })
                ->with(['items','customizeOrderItems','orderItems','flowerRequestItems','flowerItems','lines'])
                ->get();

            foreach ($orders as $order) {
                // pick the first existing items relation
                $items = $order->items
                    ?? $order->customizeOrderItems
                    ?? $order->orderItems
                    ?? $order->flowerRequestItems
                    ?? $order->flowerItems
                    ?? $order->lines
                    ?? collect();

                foreach ($items as $it) {
                    // name from multiple possibilities
                    $name = trim((string)(
                        $it->item_name
                        ?? $it->flower_name
                        ?? $it->name
                        ?? optional($it->flower)->name
                        ?? ''
                    ));
                    if ($name === '') continue;

                    $sym = $this->normalizeUnitKey((string)(
                        $it->unit
                        ?? $it->flower_unit
                        ?? $it->unit_name
                        ?? 'pcs'
                    ));

                    $qty = (float)(
                        $it->quantity
                        ?? $it->flower_quantity
                        ?? 0
                    );

                    $price = isset($it->price) ? (float)$it->price : null;

                    $key = strtolower($name).'|'.$sym;
                    if (!isset($customizeAgg[$key])) {
                        $customizeAgg[$key] = ['name'=>$name,'sym'=>$sym,'qty'=>0.0,'price'=>$price];
                    }
                    $customizeAgg[$key]['qty'] += $qty;
                    if ($price !== null) $customizeAgg[$key]['price'] = $price;
                }
            }
        }

        foreach ($customizeAgg as $agg) {
            $name   = $agg['name'];
            $sym    = $agg['sym'];
            $qty    = $agg['qty'];
            $price  = $agg['price'];

            $prefillRows[] = [
                'flower_id'    => $flowerNameToId[$name] ?? null,
                'est_quantity' => $qty,
                'unit_id'      => $unitSymbolToId[$sym] ?? null,
                'quantity'     => $qty,
                'price'        => $price,
                'flower_name'  => $name,
                'unit_label'   => $sym,
                'source'       => 'customize', // <- badge in UI
            ];
        }

        // ==========================================================================
        // 3) SUBSCRIPTION ESTIMATE (tomorrow) — append, skipping duplicates
        // ==========================================================================
        $subs     = $this->fetchActiveSubsEffectiveOn($date);
        $estimate = $this->buildEstimateForSubsOnDate($subs, $date);
        $totals   = array_values($estimate['totals_by_item'] ?? []);

        foreach ($totals as $row) {
            $name     = trim($row['item_name'] ?? '');
            $dispUnit = strtolower((string)($row['total_unit_disp'] ?? ''));

            $dupKey = strtolower($name) . '|' . $dispUnit;
            $exists = collect($prefillRows)->contains(function($r) use ($dupKey){
                return strtolower((string)($r['flower_name'] ?? '')) . '|' . strtolower((string)($r['unit_label'] ?? '')) === $dupKey;
            });
            if ($exists) continue;

            $prefillRows[] = [
                'flower_id'    => $flowerNameToId[$name] ?? null,
                'est_quantity' => $row['total_qty_disp'] ?? null,
                'unit_id'      => $unitSymbolToId[$dispUnit] ?? null,
                'quantity'     => null,
                'price'        => null,
                'flower_name'  => $name,
                'unit_label'   => $dispUnit,
                'source'       => 'estimate',
            ];
        }

        // ===== Unit id -> canonical symbol for JS ==================================
        $unitIdToSymbol = [];
        foreach ($units as $u) {
            $unitIdToSymbol[$u->id] = $this->normalizeUnitKey($u->unit_name);
        }

        return view('admin.reports.create-from-estimate', [
            'prefillDate'        => $date->toDateString(),
            'vendors'            => $vendors,
            'riders'             => $riders,
            'flowers'            => $flowers,
            'units'              => $units,
            'prefillRows'        => $prefillRows,
            'todayDate'          => Carbon::today()->toDateString(),
            'fdProductPricing'   => $fdProductPricing,
            'unitIdToSymbol'     => $unitIdToSymbol,
        ]);
    }

    public function saveFlowerPickupAssignRider(Request $request)
    {
        try {
            // 1) Validate inputs
            $validator = Validator::make($request->all(), [
                // Optional header defaults
                'vendor_id'     => 'nullable|exists:flower__vendor_details,vendor_id',
                'pickup_date'   => 'required|date',
                'delivery_date' => 'required|date|after_or_equal:pickup_date',
                'rider_id'      => 'nullable|exists:flower__rider_details,rider_id',

                // Items
                'flower_id'     => 'required|array|min:1',
                'flower_id.*'   => 'required|exists:flower_products,product_id',

                // ESTIMATE
                'est_unit_id'    => 'sometimes|array',
                'est_unit_id.*'  => 'nullable|exists:pooja_units,id',
                'est_quantity'   => 'sometimes|array',
                'est_quantity.*' => 'nullable|numeric|min:0.01',

                // ACTUAL
                'unit_id'       => 'sometimes|array',
                'unit_id.*'     => 'nullable|exists:pooja_units,id',
                'quantity'      => 'sometimes|array',
                'quantity.*'    => 'nullable|numeric|min:0.01',
                'price'         => 'sometimes|array',
                'price.*'       => 'nullable|numeric|min:0',

                // Per-row grouping helpers (not stored on items)
                'row_vendor_id'   => 'sometimes|array',
                'row_vendor_id.*' => 'nullable|exists:flower__vendor_details,vendor_id',
                'row_rider_id'    => 'sometimes|array',
                'row_rider_id.*'  => 'nullable|exists:flower__rider_details,rider_id',
            ]);

            // Custom rule: each row must resolve to a vendor (from row or header).
            $validator->after(function ($v) use ($request) {
                $flowerIds    = $request->input('flower_id', []);
                $rowVendors   = $request->input('row_vendor_id', []);
                $headerVendor = $request->input('vendor_id');

                foreach ($flowerIds as $i => $fid) {
                    $resolvedVendor = $rowVendors[$i] ?? $headerVendor;
                    if (empty($resolvedVendor)) {
                        $v->errors()->add("row_vendor_id.$i", 'Vendor is required (set per-row or select a header vendor).');
                    }
                }
            });

            $validator->validate();

            // 2) Read arrays
            $flowerIds   = $request->input('flower_id', []);
            $estUnits    = $request->input('est_unit_id', []);
            $estQtys     = $request->input('est_quantity', []);
            $unitIds     = $request->input('unit_id', []);
            $qtys        = $request->input('quantity', []);
            $prices      = $request->input('price', []);
            $rowVendors  = $request->input('row_vendor_id', []);
            $rowRiders   = $request->input('row_rider_id', []);

            $headerVendorId = $request->input('vendor_id');
            $headerRiderId  = $request->input('rider_id');

            // 3) Group rows by resolved vendor
            $groups = []; // vendor_id => ['rows'=>[], 'row_riders'=>[]]
            foreach ($flowerIds as $i => $flowerId) {
                $vendorId = $rowVendors[$i] ?? $headerVendorId; // ensured by validator
                $rowRider = $rowRiders[$i] ?? null;             // optional hint for header rider

                if (!isset($groups[$vendorId])) {
                    $groups[$vendorId] = ['rows' => [], 'row_riders' => []];
                }

                $groups[$vendorId]['rows'][] = [
                    'flower_id'    => $flowerId,
                    // Estimate
                    'est_unit_id'  => $estUnits[$i]   ?? null,
                    'est_quantity' => isset($estQtys[$i]) ? (float)$estQtys[$i] : null,
                    // Actual
                    'unit_id'      => $unitIds[$i]    ?? null,
                    'quantity'     => isset($qtys[$i])   ? (float)$qtys[$i]   : null,
                    'price'        => isset($prices[$i]) ? (float)$prices[$i] : null,
                ];

                if ($rowRider) {
                    $groups[$vendorId]['row_riders'][$rowRider] = true;
                }
            }

            // 4) Save per vendor (one header per vendor). Rider goes on header only.
            DB::transaction(function () use ($request, $groups, $headerRiderId) {
                foreach ($groups as $vendorId => $bundle) {
                    $rows     = $bundle['rows'];
                    $riderIds = array_keys($bundle['row_riders']);

                    // pick header rider for this vendor
                    $headerRiderForVendor = $headerRiderId ?: ($riderIds[0] ?? null);

                    // If DB requires NOT NULL rider_id, enforce here
                    if (is_null($headerRiderForVendor)) {
                        throw ValidationException::withMessages([
                            'rider_id' => ['Rider is required (set a header rider or at least one per-row rider for each vendor).']
                        ]);
                    }

                    $pickUpId = 'PICKUP-' . strtoupper(uniqid());

                    $pickup = FlowerPickupDetails::create([
                        'pick_up_id'     => $pickUpId,
                        'vendor_id'      => $vendorId,
                        'pickup_date'    => $request->pickup_date,
                        'delivery_date'  => $request->delivery_date,
                        'rider_id'       => $headerRiderForVendor,
                        'total_price'    => 0,
                        'payment_method' => null,
                        'payment_status' => 'pending',
                        'status'         => 'pending',
                        'payment_id'     => null,
                    ]);

                    $vendorTotal = 0.0;

                    foreach ($rows as $row) {
                        $itemTotal = (!is_null($row['price']) && !is_null($row['quantity']))
                            ? ($row['price'] * $row['quantity'])
                            : null;

                        FlowerPickupItems::create([
                            'pick_up_id'       => $pickUpId,
                            'flower_id'        => $row['flower_id'],

                            // Estimate
                            'est_unit_id'      => $row['est_unit_id'],
                            'est_quantity'     => $row['est_quantity'],

                            // Actual
                            'unit_id'          => $row['unit_id'],
                            'quantity'         => $row['quantity'] ?? 0,
                            'price'            => $row['price'],

                            // no vendor_id / rider_id on items
                            'item_total_price' => $itemTotal,
                        ]);

                        if (!is_null($itemTotal)) {
                            $vendorTotal += $itemTotal;
                        }
                    }

                    $pickup->update(['total_price' => $vendorTotal]);
                }
            });

            return redirect()
                ->back()
                ->with('success', 'Flower pickups saved vendor-wise (vendor & rider stored on header only).');

        } catch (ValidationException $e) {
            // Redirect back with validation errors AND a generic error toast
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the highlighted errors and try again.');
        } catch (\Throwable $e) {
            report($e); // log it
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while saving pickups. Please try again.');
        }
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

     public function itemCalculation(Request $request)
    {
        // Use resolveRange so preset chips work
        $preset = $request->string('preset')->toString() ?: null;
        [$start, $end] = $this->resolveRange($request, $preset);

        // Query pickups (no vendor/rider filters anymore)
        $pickups = FlowerPickupDetails::with([
                'vendor:vendor_id,vendor_name',
                'rider:rider_id,rider_name',
                'flowerPickupItems' => function ($q) {
                    $q->with([
                        'flower:product_id,name',
                        'estUnit:id,unit_name',
                        'unit:id,unit_name',
                    ])->orderBy('id');
                },
            ])
            ->whereBetween('pickup_date', [$start, $end]) // keep full datetime range
            ->orderBy('vendor_id')
            ->orderBy('pickup_date', 'desc')
            ->orderBy('pick_up_id', 'desc')
            ->paginate(20);

        $unitMap = PoojaUnit::pluck('unit_name', 'id')->toArray();

        // Ensure Carbon instances for view formatting
        $pickups->getCollection()->transform(function ($p) {
            if ($p->pickup_date && !($p->pickup_date instanceof \Carbon\Carbon)) {
                $p->pickup_date = \Carbon\Carbon::parse($p->pickup_date);
            }
            if ($p->delivery_date && !($p->delivery_date instanceof \Carbon\Carbon)) {
                $p->delivery_date = \Carbon\Carbon::parse($p->delivery_date);
            }
            return $p;
        });

        return view('admin.reports.flower-estimate-calculation', [
            'pickups'  => $pickups,
            'start'    => $start->toDateString(), // for <input type="date">
            'end'      => $end->toDateString(),
            'unitMap'  => $unitMap,
            'preset'   => $preset ?? '',
            'sheetTitlePrefix' => 'Pickups',
        ]);
    }

    private function resolveRange(Request $request, ?string $preset): array
    {
        $today = Carbon::today();

        if ($preset === 'today')      return [$today->copy()->startOfDay(), $today->copy()->endOfDay()];
        if ($preset === 'yesterday') { $y=$today->copy()->subDay(); return [$y->startOfDay(), $y->endOfDay()]; }
        if ($preset === 'tomorrow')  { $t=$today->copy()->addDay(); return [$t->startOfDay(), $t->endOfDay()]; }
        if ($preset === 'this_week')  return [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()->endOfDay()];
        if ($preset === 'this_month') return [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()->endOfDay()];

        $start = $request->filled('start') ? Carbon::parse($request->input('start'))->startOfDay() : $today->copy()->startOfDay();
        $end   = $request->filled('end')   ? Carbon::parse($request->input('end'))->endOfDay()   : $today->copy()->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }
        return [$start, $end];
    }
}
