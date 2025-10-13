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

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class FlowerPickupController extends Controller
{

    public function addflowerpickupdetails()
    {
      
        $flowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
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

    /**
     * DataTables server-side JSON
     */
    public function ajaxFlowerPickupDetails(Request $request)
    {
        try {
            $draw   = (int) $request->input('draw', 1);
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 25);
            $search = trim((string) $request->input('search.value', ''));
            $filter = (string) $request->input('filter', 'all');
            $order  = $request->input('order', []);

            // Build base query using Eloquent + relations (avoids hard-coded table names)
            $base = FlowerPickupDetails::query()
                ->with([
                    // select only what we need, and match your custom PKs
                    'vendor:vendor_id,vendor_name',
                    'rider:rider_id,rider_name',
                ]);

            // Filters on the details table
            switch ($filter) {
                case 'todayexpenses':
                    $base->whereDate('pickup_date', Carbon::today());
                    break;
                case 'todaypaidpickup':
                    $base->whereDate('pickup_date', Carbon::today())
                         ->where('payment_status', 'Paid');
                    break;
                case 'todaypendingpickup':
                    $base->whereDate('pickup_date', Carbon::today())
                         ->where('payment_status', 'pending');
                    break;
                case 'monthlyexpenses':
                    $base->whereMonth('pickup_date', Carbon::now()->month)
                         ->whereYear('pickup_date', Carbon::now()->year);
                    break;
                case 'monthlypaidpickup':
                    $base->whereMonth('pickup_date', Carbon::now()->month)
                         ->whereYear('pickup_date', Carbon::now()->year)
                         ->where('payment_status', 'Paid');
                    break;
                case 'monthlypendingpickup':
                    $base->whereMonth('pickup_date', Carbon::now()->month)
                         ->whereYear('pickup_date', Carbon::now()->year)
                         ->where('payment_status', 'pending');
                    break;
                default:
                    // 'all' -> no extra filter
                    break;
            }

            // Total before search
            $recordsTotal = (clone $base)->count('id');

            // Search across pick_up_id, payment_status, status + related vendor/rider names
            if ($search !== '') {
                $like = '%' . strtr($search, ['%' => '\%', '_' => '\_']) . '%';

                $base->where(function ($q) use ($like) {
                    $q->where('pick_up_id', 'like', $like)
                      ->orWhere('payment_status', 'like', $like)
                      ->orWhere('status', 'like', $like)
                      ->orWhereHas('vendor', function ($vq) use ($like) {
                          $vq->where('vendor_name', 'like', $like);
                      })
                      ->orWhereHas('rider', function ($rq) use ($like) {
                          $rq->where('rider_name', 'like', $like);
                      });
                });
            }

            // Filtered count after search
            $recordsFiltered = (clone $base)->count('id');

            // Safe ordering: allow only fields that live on the details table
          // Safe ordering map (unchanged)
$safeOrderMap = [
    1 => 'pick_up_id',
    5 => 'pickup_date',
    6 => 'total_price',
    7 => 'payment_status',
    8 => 'status',
];

// ✅ Default sort by numeric primary key
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

            // Fetch paginated rows
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
                    'total_price',
                    'payment_status',
                    'status',
                ]);

            // Transform for DataTables
            $data = $rows->map(function ($r, $i) use ($start) {
                $idx   = $start + $i + 1;
                $date  = $r->pickup_date ? Carbon::parse($r->pickup_date)->format('d-m-Y') : 'N/A';

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

                return [
                    $idx,                                   // #
                    e($r->pick_up_id ?? 'N/A'),            // Pickup Id
                    e(optional($r->vendor)->vendor_name ?? 'N/A'), // Vendor
                    e(optional($r->rider)->rider_name ?? 'N/A'),   // Rider
                    $viewBtn,                              // Flower Details
                    $date,                                 // PickUp Date
                    $price,                                // Total Price
                    $payBadge,                             // Payment Status
                    $statusBadge,                          // Status
                    '<div class="d-flex align-items-center gap-2">'.$actions.'</div>', // Actions
                ];
            })->values()->toArray();

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ], 200, ['Content-Type' => 'application/json']);

        } catch (Throwable $e) {
            Log::error('DT ajaxFlowerPickupDetails failed', [
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

    /**
     * Items list for the modal.
     * Your items are linked by pick_up_id (NOT the numeric id),
     * so we fetch the detail first, then query items by its pick_up_id.
     */
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
            'status' => 'PickupCompleted',
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
    
    public function saveFlowerPickupAssignRider(Request $request)
{
    // Validate the request
    $request->validate([
        'vendor_id'     => 'required|exists:flower__vendor_details,vendor_id',
        'pickup_date'   => 'required|date',
        'delivery_date' => 'required|date|after_or_equal:pickup_date', // ✅ NEW
        'rider_id'      => 'required|exists:flower__rider_details,rider_id',

        'flower_id'     => 'required|array',
        'flower_id.*'   => 'required|exists:flower_products,product_id',

        'unit_id'       => 'required|array',
        'unit_id.*'     => 'required|exists:pooja_units,id',

        'quantity'      => 'required|array',
        'quantity.*'    => 'required|numeric|min:0.01',

        // price is optional and may be missing entirely
        'price'         => 'sometimes|array',
        'price.*'       => 'nullable|numeric|min:0',
    ]);

    // Generate unique pick_up_id
    $pickUpId = 'PICKUP-' . strtoupper(uniqid());

    // Create the pickup (now includes delivery_date)
    $pickup = FlowerPickupDetails::create([
        'pick_up_id'     => $pickUpId,
        'vendor_id'      => $request->vendor_id,
        'pickup_date'    => $request->pickup_date,
        'delivery_date'  => $request->delivery_date, // ✅ NEW
        'rider_id'       => $request->rider_id,
        'total_price'    => 0, // will calculate below
        'payment_method' => null,
        'payment_status' => 'pending',
        'status'         => 'pending',
        'payment_id'     => null,
    ]);

    // Read arrays safely (avoid undefined index notices)
    $flowerIds = $request->input('flower_id', []);
    $unitIds   = $request->input('unit_id',   []);
    $qtys      = $request->input('quantity',  []);
    $prices    = $request->input('price',     []); // may not exist; defaults to []

    $totalPrice = 0;

    foreach ($flowerIds as $i => $flowerId) {
        $unitId   = $unitIds[$i]   ?? null;
        $quantity = isset($qtys[$i])   ? (float)$qtys[$i]   : null;
        $price    = isset($prices[$i]) ? (float)$prices[$i] : null; // nullable

        // Create each item row
        FlowerPickupItems::create([
            'pick_up_id' => $pickUpId,
            'flower_id'  => $flowerId,
            'unit_id'    => $unitId,
            'quantity'   => $quantity ?? 0,
            'price'      => $price,         // nullable
        ]);

        // Accumulate total only if price provided
        if ($price !== null && $quantity !== null) {
            $totalPrice += $price * $quantity; // ✅ use price × quantity
        }
    }

    // Update total on header row
    $pickup->update(['total_price' => $totalPrice]);

    return redirect()
        ->back()
        ->with('success', 'Flower pickup details saved successfully!');
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_id' => 'required',
            'pickup_date' => 'required|date',
            'flower_id.*' => 'required',
            'unit_id.*' => 'required',
            'quantity.*' => 'required|numeric',
            // 'price.*' => 'required|numeric',
            'rider_id' => 'required',
        ]);

        $pickup = FlowerPickupDetails::findOrFail($id);

        // Update pickup details
        $pickup->update([
            'vendor_id' => $request->vendor_id,
            'pickup_date' => $request->pickup_date,
            'rider_id' => $request->rider_id,
        ]);

        $totalPrice = 0;

        // Update flower items
        foreach ($request->flower_id as $index => $flowerId) {
            $quantity = $request->quantity[$index];
            $price = $request->price[$index];
            $totalPrice +=  $price;

            FlowerPickupItems::updateOrCreate(
                ['pick_up_id' => $pickup->pick_up_id, 'flower_id' => $flowerId],
                [
                    'unit_id' => $request->unit_id[$index],
                    'quantity' => $quantity,
                    'price' => $price,
                ]
            );
        }

        // Update total price in the details table
        $pickup->update(['total_price' => $totalPrice]);

        return redirect()->route('admin.manageflowerpickupdetails')->with('success', 'Flower Pickup updated successfully.');
    }

    public function updatePayment(Request $request, $pickup_id)
    {
        // Find the pickup detail by ID
        $pickupDetail = FlowerPickupDetails::findOrFail($pickup_id);

        // Update the payment details
        $pickupDetail->payment_status = 'Paid';
        $pickupDetail->Status = 'Completed';

        $pickupDetail->payment_method = $request->input('payment_method');
        $pickupDetail->payment_id = $request->input('payment_id');
        $pickupDetail->paid_by = $request->input('paid_by', 'N/A');
        $pickupDetail->save();

        // Log the payment update
        Log::info('Payment updated', [
            'pickup_id' => $pickup_id,
            'payment_method' => $request->input('payment_method'),
            'payment_id' => $request->input('payment_id'),
            'paid_by' => $request->input('paid_by', 'N/A')
        ]);

        return redirect()->back()->with('success', 'Payment details updated successfully');
    }

}
