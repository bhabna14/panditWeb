<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerPickupRequest;
use App\Models\OfficeTransaction;
use App\Models\OfficeLedger;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;  

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class FlowerPickupController extends Controller
{

    public function addflowerpickupdetails()
    {
        $flowers = FlowerProduct::where('status', 'active')->where('category', 'Flower')->get();
        $units = PoojaUnit::where('status', 'active')->get();
        $vendors = FlowerVendor::where('status', 'active')->get();
        $riders = RiderDetails::where('status', 'active')->get();

        return view('admin.flower-pickup-details.add-flower-pickup-details', compact('flowers', 'units', 'vendors', 'riders'));
    }

    public function addflowerpickuprequest()
    {
      
        $flowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
        $units = PoojaUnit::where('status', 'active')->get();
        $vendors = FlowerVendor::where('status', 'active')->get();
        $riders = RiderDetails::where('status', 'active')->get();
        $pickuprequests = FlowerPickupRequest::where('status', 'pending')->get();

    
        return view('admin.flower-pickup-details.add-flower-pickup-request', compact('pickuprequests','flowers', 'units', 'vendors', 'riders'));
    }

    public function approveRequest($id)
    {
        $request = FlowerPickupRequest::find($id);

        if ($request) {
            $request->status = 'approved';
            $request->save();

            return redirect()->back()->with('success', 'Pickup request approved successfully.');
        }

        return redirect()->back()->with('error', 'Pickup request not found.');
    }
    
    public function manageflowerpickupdetails(Request $request)
    {
        // Use your real table name via the model (double underscore is fine)
        $totalExpensesday = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())
            ->sum('total_price');

        return view('admin.flower-pickup-details.manage-flower-pickup-details', compact('totalExpensesday'));
    }

    public function ajaxFlowerPickupDetails(Request $request)
    {
        try {
            $draw   = (int) $request->input('draw', 1);
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 25);
            $search = trim((string) $request->input('search.value', ''));
            $filter = (string) $request->input('filter', 'all');
            $order  = $request->input('order', []);

            $base = FlowerPickupDetails::query()
                ->with([
                    'vendor:vendor_id,vendor_name',
                    'rider:rider_id,rider_name',
                ]);

            switch ($filter) {
                case 'todayexpenses':
                    $base->whereDate('pickup_date', \Carbon\Carbon::today());
                    break;
                case 'todaypaidpickup':
                    $base->whereDate('pickup_date', \Carbon\Carbon::today())
                        ->where('payment_status', 'Paid');
                    break;
                case 'todaypendingpickup':
                    $base->whereDate('pickup_date', \Carbon\Carbon::today())
                        ->where('payment_status', 'pending');
                    break;
                case 'monthlyexpenses':
                    $base->whereMonth('pickup_date', \Carbon\Carbon::now()->month)
                        ->whereYear('pickup_date', \Carbon\Carbon::now()->year);
                    break;
                case 'monthlypaidpickup':
                    $base->whereMonth('pickup_date', \Carbon\Carbon::now()->month)
                        ->whereYear('pickup_date', \Carbon\Carbon::now()->year)
                        ->where('payment_status', 'Paid');
                    break;
                case 'monthlypendingpickup':
                    $base->whereMonth('pickup_date', \Carbon\Carbon::now()->month)
                        ->whereYear('pickup_date', \Carbon\Carbon::now()->year)
                        ->where('payment_status', 'pending');
                    break;
                default:
                    // no-op
                    break;
            }

            $recordsTotal = (clone $base)->count('id');

            if ($search !== '') {
                $like = '%' . strtr($search, ['%' => '\%', '_' => '\_']) . '%';
                $base->where(function ($q) use ($like) {
                    $q->where('pick_up_id', 'like', $like)
                    ->orWhere('payment_status', 'like', $like)
                    ->orWhere('status', 'like', $like)
                    ->orWhereHas('vendor', fn($vq) => $vq->where('vendor_name', 'like', $like))
                    ->orWhereHas('rider', fn($rq) => $rq->where('rider_name', 'like', $like));
                });
            }

            $recordsFiltered = (clone $base)->count('id');

            // ✅ Updated safe order map to match your header indexes:
            // 0:# 1:Pickup Id 2:Vendor 3:Rider 4:Flower Details 5:PickUp Date 6:Delivery Date
            // 7:Total Price 8:Payment Status 9:Status 10:Actions
            $safeOrderMap = [
                1 => 'pick_up_id',
                5 => 'pickup_date',
                6 => 'delivery_date',   // ✅ new
                7 => 'total_price',
                8 => 'payment_status',
                9 => 'status',
            ];

            $orderBy = 'id';
            $dir     = 'desc';

            if (!empty($order[0])) {
                $colIdx = (int)($order[0]['column'] ?? 5);
                $dirRaw = strtolower($order[0]['dir'] ?? 'desc');
                $dir    = in_array($dirRaw, ['asc', 'desc'], true) ? $dirRaw : 'desc';
                if (isset($safeOrderMap[$colIdx])) {
                    $orderBy = $safeOrderMap[$colIdx];
                }
            }

            $rows = (clone $base)
                ->orderBy($orderBy, $dir)
                ->skip($start)
                ->take($length)
                ->get([
                    'id',
                    'pick_up_id',
                    'vendor_id',
                    'rider_id',
                    'pickup_date',
                    'delivery_date',   // ✅ select it
                    'total_price',
                    'payment_status',
                    'status',
                ]);

            $data = $rows->map(function ($r, $i) use ($start) {
                $idx   = $start + $i + 1;
                $pDate = $r->pickup_date
                    ? \Carbon\Carbon::parse($r->pickup_date)->format('d-m-Y')
                    : 'N/A';

                $dDate = $r->delivery_date
                    ? \Carbon\Carbon::parse($r->delivery_date)->format('d-m-Y')
                    : '<span class="text-warning">Pending</span>';

                $price = ($r->total_price !== null && $r->total_price !== '')
                    ? '₹' . number_format((float) $r->total_price, 2)
                    : '<span class="text-warning">Pending</span>';

                $payBadge = ($r->payment_status === 'Paid')
                    ? '<span class="badge bg-success" style="font-size:12px;width:70px;padding:10px">Paid</span>'
                    : '<span class="badge bg-danger" style="font-size:12px;width:70px;padding:10px">Unpaid</span>';

                if ($r->status === 'pending') {
                    $statusBadge = '<span class="badge bg-danger" style="font-size:12px;width:100px;padding:10px"><i class="fas fa-hourglass-half"></i> Pending</span>';
                } elseif ($r->status === 'Completed') {
                    $statusBadge = '<span class="badge bg-success" style="font-size:12px;width:100px;padding:10px"><i class="fas fa-check-circle"></i> Completed</span>';
                } else {
                    $statusText  = e($r->status ?: 'N/A');
                    $statusBadge = '<span class="badge bg-secondary"><i class="fas fa-question-circle"></i> ' . $statusText . '</span>';
                }

                $viewBtn = '<button type="button" class="btn btn-primary btn-view-items" data-id="' . e($r->id) . '" data-bs-toggle="modal" data-bs-target="#flowerDetailsModal">
                                <i class="fas fa-eye"></i> View
                            </button>';

                $actions = '
                    <a href="' . e(route('flower-pickup.edit', $r->id)) . '" class="btn btn-primary d-flex align-items-center justify-content-center" style="width:40px;padding:10px;font-size:12px;">
                        <i class="fas fa-edit me-1"></i>
                    </a>
                    <button class="btn btn-secondary d-flex align-items-center justify-content-center btn-open-payment"
                            style="width:40px;padding:10px;font-size:12px;"
                            data-id="' . e($r->id) . '"
                            data-action="' . e(route('update.payment', $r->id)) . '"
                            data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-credit-card me-1"></i>
                    </button>';

                // ✅ Return array aligned with your table columns (indexes 0..10):
                return [
                    $idx,                                   // 0: #
                    e($r->pick_up_id ?? 'N/A'),            // 1: Pickup Id
                    e(optional($r->vendor)->vendor_name ?? 'N/A'), // 2: Vendor
                    e(optional($r->rider)->rider_name ?? 'N/A'),   // 3: Rider
                    $viewBtn,                              // 4: Flower Details
                    $pDate,                                // 5: PickUp Date
                    $dDate,                                // 6: Delivery Date  ✅ new slot
                    $price,                                // 7: Total Price    (shifted)
                    $payBadge,                             // 8: Payment Status (shifted)
                    $statusBadge,                          // 9: Status         (shifted)
                    '<div class="d-flex align-items-center gap-2">'.$actions.'</div>', // 10: Actions
                ];
            })->values()->toArray();

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ], 200, ['Content-Type' => 'application/json']);

        } catch (\Throwable $e) {
            \Log::error('DT ajaxFlowerPickupDetails failed', [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (config('app.debug')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getFlowerPickupItems(int $id)
    {
        $detail = FlowerPickupDetails::findOrFail($id);

        $items = FlowerPickupItems::with(['flower:product_id,name', 'unit:id,unit_name'])
        ->where('pick_up_id', $detail->pick_up_id)
        ->get()
        ->map(function ($it) {
            return [
                'flower'   => $it->flower->name ?? 'N/A',
                'quantity' => $it->quantity ?? 'N/A',
                'unit'     => $it->unit->unit_name ?? 'N/A',
                'price'    => $it->price ?? 'N/A',
            ];
        })
        ->values()
        ->toArray();

        return response()->json(['items' => $items], 200, ['Content-Type' => 'application/json']);
    }

    public function edit($id)
    {
        $detail = FlowerPickupDetails::with(['flowerPickupItems', 'vendor', 'rider'])->findOrFail($id);
       
        $flowers = FlowerProduct::where('status', 'active')
                    ->where('category', 'Flower')
                    ->get();
        $units = PoojaUnit::where('status', 'active')->get();
        $vendors = FlowerVendor::where('status', 'active')->get();
        $riders = RiderDetails::where('status', 'active')->get();

        return view('admin.flower-pickup-details.edit-flower-pickup-details', compact('detail', 'vendors', 'flowers', 'units', 'riders'));
    }

    public function saveFlowerPickupDetails(Request $request)
    {
        // Validate the request
        $request->validate([
            'vendor_id' => 'required|exists:flower__vendor_details,vendor_id',
            'pickup_date' => 'required|date',
            'rider_id' => 'required|exists:flower__rider_details,rider_id',
            'flower_id' => 'required|array',
            'flower_id.*' => 'required|exists:flower_products,product_id',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|exists:pooja_units,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ]);
    
        // Generate unique pick_up_id
        $pickUpId = 'PICKUP-' . strtoupper(uniqid());
    
        // Save main flower pickup details
        $pickup = FlowerPickupDetails::create([
            'pick_up_id' => $pickUpId,
            'vendor_id' => $request->vendor_id,
            'pickup_date' => $request->pickup_date,
            'rider_id' => $request->rider_id,
            'total_price' => 0, // Will calculate later
            'payment_method' => null,
            'payment_status' => 'pending',
            'status' => 'pending',
            'payment_id' => null,
        ]);
    
        // Initialize total price
        $totalPrice = 0;
    
        // Save flower items and calculate total price
        foreach ($request->flower_id as $index => $flowerId) {
            $quantity = $request->quantity[$index];
            $price = $request->price[$index];
            $unitId = $request->unit_id[$index];
    
            // Insert flower details into FlowerPickupItems table
            FlowerPickupItems::create([
                'pick_up_id' => $pickUpId,
                'flower_id' => $flowerId,
                'unit_id' => $unitId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
    
            // Accumulate total price
            $totalPrice += $price;
        }
    
        // Update total price in FlowerPickupDetails
        $pickup->update(['total_price' => $totalPrice]);
    
        return redirect()->back()->with('success', 'Flower pickup details saved successfully!');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_id'   => 'required',
            'pickup_date' => 'required|date',

            'flower_id'   => 'required|array|min:1',
            'flower_id.*' => 'required',

            'unit_id'     => 'required|array',
            'unit_id.*'   => 'required',

            'quantity'    => 'required|array',
            'quantity.*'  => 'required|numeric|min:0',

            // Price: decimals allowed, 0.0 allowed, can be left empty
            'price'       => 'nullable|array',
            'price.*'     => 'nullable|numeric|min:0',

            'rider_id'    => 'required',
        ]);

        /** @var \App\Models\FlowerPickupDetails $pickup */
        $pickup = FlowerPickupDetails::findOrFail($id);

        // Update header fields
        $pickup->update([
            'vendor_id'   => $request->vendor_id,
            'pickup_date' => $request->pickup_date,
            'rider_id'    => $request->rider_id,
        ]);

        $totalPrice         = 0;
        $flowerIdsInRequest = [];

        // Sync flower items
        foreach ($request->flower_id as $index => $flowerId) {
            $flowerIdsInRequest[] = $flowerId;

            $quantity = $request->quantity[$index] ?? 0;
            $price    = $request->price[$index] ?? 0;

            // Sum only the price column (row amount)
            $totalPrice += (float) $price;

            FlowerPickupItems::updateOrCreate(
                [
                    'pick_up_id' => $pickup->pick_up_id,
                    'flower_id'  => $flowerId,
                ],
                [
                    'unit_id'  => $request->unit_id[$index] ?? null,
                    'quantity' => $quantity,
                    'price'    => $price,
                ]
            );
        }

        // Optionally delete removed items (if you want to keep DB in sync)
        FlowerPickupItems::where('pick_up_id', $pickup->pick_up_id)
            ->whereNotIn('flower_id', $flowerIdsInRequest)
            ->delete();

        // Update total price in the main table
        $pickup->update([
            'total_price' => $totalPrice,
        ]);

        return redirect()
            ->route('admin.manageflowerpickupdetails')
            ->with('success', 'Flower Pickup updated successfully.');
    }

    public function updatePayment(Request $request, $pickup_id)
    {
        // Basic validation (optional but recommended)
        $request->validate([
            'payment_method' => 'required|string',
            'paid_by'        => 'required|string',
            'payment_id'     => 'nullable|string',
        ]);

        $tz = config('app.timezone', 'Asia/Kolkata');

        try {
            DB::transaction(function () use ($request, $pickup_id, $tz) {
                // 1) Lock + update pickup payment
                /** @var \App\Models\FlowerPickupDetails $pickupDetail */
                $pickupDetail = FlowerPickupDetails::lockForUpdate()->findOrFail($pickup_id);

                // Fix: column is `status`, NOT `Status`
                $pickupDetail->payment_status = 'Paid';
                $pickupDetail->status         = 'Completed';

                $pickupDetail->payment_method = $request->input('payment_method');
                $pickupDetail->payment_id     = $request->input('payment_id');
                $pickupDetail->paid_by        = $request->input('paid_by', 'N/A');
                $pickupDetail->save();

                // 2) Create OfficeTransaction row (expense: vendor_payment)
                $vendorName = optional($pickupDetail->vendor)->vendor_name
                    ?? optional($pickupDetail->vendor)->name
                    ?? 'Vendor';

                $description = sprintf(
                    'Vendor payment for pickup %s (%s)',
                    $pickupDetail->pick_up_id,
                    $vendorName
                );

                $officeTransaction = OfficeTransaction::create([
                    'date'           => Carbon::today($tz)->format('Y-m-d'),
                    'paid_by'        => $pickupDetail->paid_by,
                    'amount'         => $pickupDetail->total_price,   // outgoing amount
                    'mode_of_payment'=> strtolower($pickupDetail->payment_method), // e.g. cash/upi/online
                    'categories'     => 'vendor_payment',             // matches your blade filter option
                    'description'    => $description,
                    'status'         => 'active',
                ]);

                // 3) Mirror it into OfficeLedger as an OUT entry
                OfficeLedger::create([
                    'entry_date'     => $officeTransaction->date,
                    'category'       => $officeTransaction->categories,
                    'direction'      => 'out',                       // money going out
                    'source_type'    => 'transaction',
                    'source_id'      => $officeTransaction->id,
                    'amount'         => $officeTransaction->amount,
                    'mode_of_payment'=> $officeTransaction->mode_of_payment,
                    'paid_by'        => $officeTransaction->paid_by,
                    'received_by'    => $vendorName,
                    'description'    => $officeTransaction->description,
                    'status'         => 'active',
                ]);

                // 4) Log everything for debugging
                Log::info('Vendor payment updated & pushed to ledger', [
                    'pickup_id'          => $pickupDetail->id,
                    'pick_up_code'       => $pickupDetail->pick_up_id,
                    'vendor_id'          => $pickupDetail->vendor_id,
                    'vendor_name'        => $vendorName,
                    'payment_method'     => $pickupDetail->payment_method,
                    'payment_id'         => $pickupDetail->payment_id,
                    'paid_by'            => $pickupDetail->paid_by,
                    'office_transaction' => $officeTransaction->id,
                ]);
            });

            return redirect()
                ->back()
                ->with('success', 'Payment details updated & vendor payment saved to ledger.');
        } catch (\Throwable $e) {
            Log::error('Failed to update vendor payment / ledger', [
                'pickup_id' => $pickup_id,
                'error'     => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unable to update payment. Please try again.');
        }
    }

    public function storeFlowerPickup(Request $request)
    {
        // ---- 1) Validate top-level + array fields ---------------------------
        $validated = $request->validate([
            'vendor_id'     => 'required|exists:flower__vendor_details,vendor_id',
            'pickup_date'   => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:pickup_date',
            'rider_id'      => 'required|exists:flower__rider_details,rider_id',
            // Arrays
            'flower_id'     => 'required|array',
            'flower_id.*'   => 'nullable|exists:flower_products,product_id',
            'unit_id'       => 'required|array',
            'unit_id.*'     => 'nullable|exists:pooja_units,id',
            'quantity'      => 'required|array',
            'quantity.*'    => 'nullable|numeric|min:0.01',

            // Optional prices
            'price'         => 'sometimes|array',
            'price.*'       => 'nullable|numeric|min:0',
        ]);

        // ---- 2) Normalize & extract valid item rows -------------------------
        $flowerIds = $request->input('flower_id', []);
        $unitIds   = $request->input('unit_id',   []);
        $qtys      = $request->input('quantity',  []);
        $prices    = $request->input('price',     []);

        $items = [];
        $rowCount = max(count($flowerIds), count($unitIds), count($qtys), count($prices));

        for ($i = 0; $i < $rowCount; $i++) {
            $f = $flowerIds[$i] ?? null;
            $u = $unitIds[$i]   ?? null;
            $q = $qtys[$i]      ?? null;
            $p = $prices[$i]    ?? null;

            // keep only rows that have the minimum required trio
            if ($f && $u && $q !== null && $q !== '' && (float)$q > 0) {
                $items[] = [
                    'flower_id' => $f,
                    'unit_id'   => $u,
                    'quantity'  => (float) $q,
                    // price may be null (optional)
                    'price'     => ($p !== null && $p !== '') ? (float) $p : null,
                ];
            }
        }

        if (empty($items)) {
            return back()
                ->withErrors(['general' => 'Please add at least one valid item row (flower, unit, and quantity).'])
                ->withInput();
        }

        // ---- 3) Compute total only where price is provided ------------------
        $total = 0.0;
        foreach ($items as $it) {
            if (!is_null($it['price'])) {
                $total += $it['quantity'] * $it['price'];
            }
        }

        // ---- 4) Persist in a transaction -----------------------------------
        try {
            DB::beginTransaction();

            // robust unique pick_up_id (string)
            $pickUpId = 'PICKUP-' . strtoupper(uniqid());

            $parent = FlowerPickupDetails::create([
                'pick_up_id'    => $pickUpId,
                'vendor_id'     => $validated['vendor_id'],
                'rider_id'      => $validated['rider_id'],
                'pickup_date'   => Carbon::parse($validated['pickup_date'])->toDateString(),
                'delivery_date' => Carbon::parse($validated['delivery_date'])->toDateString(),
                'total_price'   => $total,          // Σ(price × qty) where price provided
                'payment_method'=> null,            // fill later if needed
                'paid_by'       => null,
                'payment_status'=> 'pending',       // or null / default
                'status'        => 'pending',     // or your preferred default
                'payment_id'    => null,
            ]);

            foreach ($items as $it) {
                FlowerPickupItems::create([
                    'pick_up_id' => $parent->pick_up_id,
                    'flower_id'  => $it['flower_id'],
                    'unit_id'    => $it['unit_id'],
                    'quantity'   => $it['quantity'],
                    'price'      => $it['price'], // nullable
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withErrors(['general' => 'Failed to save. Please try again or contact support.'])
                ->withInput();
        }

        // ---- 5) Done --------------------------------------------------------
        return redirect()->route('admin.manageflowerpickupdetails')->with('success', 'Flower pickup saved successfully.');

    }

}
