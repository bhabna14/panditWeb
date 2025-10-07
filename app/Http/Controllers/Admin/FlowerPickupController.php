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
        // We still compute today's total for the badge – lightweight and cached by DB
        $totalExpensesday = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())->sum('total_price');

        return view('admin.flower-pickup-details.manage-flower-pickup-details', compact('totalExpensesday'));
    }

    /**
     * Server-side data provider for DataTables.
     * Handles: search, order, paging, and your filter switch.
     */
    public function ajaxFlowerPickupDetails(Request $request)
    {
        // DataTables params
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));
        $filter = $request->input('filter', 'all');
        $order  = $request->input('order', []);

        // Map incoming column index -> DB columns (safe listing)
        $columns = [
            0 => 'fpd.id',            // #
            1 => 'fpd.pick_up_id',    // Pickup Id
            2 => 'vendors.vendor_name',
            3 => 'riders.rider_name',
            4 => 'fpd.id',            // Flower Details (not sortable; but we map to id)
            5 => 'fpd.pickup_date',
            6 => 'fpd.total_price',
            7 => 'fpd.payment_status',
            8 => 'fpd.status',
            9 => 'fpd.id',            // Actions
        ];

        // Base query with joins for fast search/sort on related cols
        $base = FlowerPickupDetails::query()
            ->from('flower_pickup_details as fpd')
            ->leftJoin('vendors', 'vendors.id', '=', 'fpd.vendor_id')
            ->leftJoin('rider_details as riders', 'riders.id', '=', 'fpd.rider_id');

        // Apply date/status filters (same semantics as your original)
        switch ($filter) {
            case 'todayexpenses':
                $base->whereDate('fpd.pickup_date', Carbon::today());
                break;
            case 'todaypaidpickup':
                $base->whereDate('fpd.pickup_date', Carbon::today())
                     ->where('fpd.payment_status', 'Paid');
                break;
            case 'todaypendingpickup':
                $base->whereDate('fpd.pickup_date', Carbon::today())
                     ->where('fpd.payment_status', 'pending');
                break;
            case 'monthlyexpenses':
                $base->whereMonth('fpd.pickup_date', Carbon::now()->month);
                break;
            case 'monthlypaidpickup':
                $base->whereMonth('fpd.pickup_date', Carbon::now()->month)
                     ->where('fpd.payment_status', 'Paid');
                break;
            case 'monthlypendingpickup':
                $base->whereMonth('fpd.pickup_date', Carbon::now()->month)
                     ->where('fpd.payment_status', 'pending');
                break;
            case 'all':
            default:
                // no-op
                break;
        }

        // Total after fixed filters (before search)
        $recordsTotal = (clone $base)->count('fpd.id');

        // Search over a few key fields
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $q->orWhere('fpd.pick_up_id', 'like', $like)
                  ->orWhere('vendors.vendor_name', 'like', $like)
                  ->orWhere('riders.rider_name', 'like', $like)
                  ->orWhere('fpd.payment_status', 'like', $like)
                  ->orWhere('fpd.status', 'like', $like);
            });
        }

        // Total after search
        $recordsFiltered = (clone $base)->count('fpd.id');

        // Ordering
        $orderBy = 'fpd.pickup_date';
        $dir     = 'desc';
        if (!empty($order[0])) {
            $colIdx = (int) ($order[0]['column'] ?? 5);
            $tmpCol = $columns[$colIdx] ?? 'fpd.pickup_date';
            if (in_array($tmpCol, $columns, true)) {
                $orderBy = $tmpCol;
            }
            $dirCandidate = strtolower($order[0]['dir'] ?? 'desc');
            $dir = in_array($dirCandidate, ['asc', 'desc'], true) ? $dirCandidate : 'desc';
        }

        // Fetch paged rows
        $rows = (clone $base)
            ->select([
                'fpd.id',
                'fpd.pick_up_id',
                'vendors.vendor_name',
                'riders.rider_name',
                'fpd.pickup_date',
                'fpd.total_price',
                'fpd.payment_status',
                'fpd.status',
            ])
            ->orderBy($orderBy, $dir)
            ->skip($start)
            ->take($length)
            ->get();

        // Transform rows to the exact table cells we need (format + action buttons)
        $data = $rows->map(function ($r, $i) use ($start) {
            $idx   = $start + $i + 1;
            $date  = $r->pickup_date ? Carbon::parse($r->pickup_date)->format('d-m-Y') : 'N/A';
            $price = $r->total_price ? '₹' . number_format((float) $r->total_price, 2) : '<span class="text-warning">Pending</span>';

            $payBadge = $r->payment_status === 'Paid'
                ? '<span class="badge bg-success" style="font-size:12px;width:70px;padding:10px">Paid</span>'
                : '<span class="badge bg-danger" style="font-size:12px;width:70px;padding:10px">Unpaid</span>';

            $statusBadge = match ($r->status) {
                'pending'   => '<span class="badge bg-danger" style="font-size:12px;width:100px;padding:10px"><i class="fas fa-hourglass-half"></i> Pending</span>',
                'Completed' => '<span class="badge bg-success" style="font-size:12px;width:100px;padding:10px"><i class="fas fa-check-circle"></i> Completed</span>',
                default     => '<span class="badge bg-secondary"><i class="fas fa-question-circle"></i> '.e($r->status ?? 'N/A').'</span>',
            };

            $viewBtn = '<button type="button" class="btn btn-primary btn-view-items" data-id="'.$r->id.'" data-bs-toggle="modal" data-bs-target="#flowerDetailsModal">
                            <i class="fas fa-eye"></i> View
                        </button>';

            $actions = '
                <a href="'.route('flower-pickup.edit', $r->id).'" class="btn btn-primary d-flex align-items-center justify-content-center" style="width:40px;padding:10px;font-size:12px;">
                    <i class="fas fa-edit me-1"></i>
                </a>
                <button class="btn btn-secondary d-flex align-items-center justify-content-center btn-open-payment"
                        style="width:40px;padding:10px;font-size:12px;"
                        data-id="'.$r->id.'"
                        data-action="'.e(route('update.payment', $r->id)).'"
                        data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-credit-card me-1"></i>
                </button>';

            return [
                $idx,
                e($r->pick_up_id ?? 'N/A'),
                e($r->vendor_name ?? 'N/A'),
                e($r->rider_name ?? 'N/A'),
                $viewBtn,
                $date,
                $price,
                $payBadge,
                $statusBadge,
                '<div class="d-flex align-items-center gap-2">'.$actions.'</div>',
            ];
        });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * Returns flower items for a pickup (for the modal).
     */
    public function getFlowerPickupItems(int $id)
    {
        $items = FlowerPickupItem::with(['flower:id,name', 'unit:id,unit_name'])
            ->where('flower_pickup_details_id', $id)
            ->get()
            ->map(function ($it) {
                return [
                    'flower'   => $it->flower->name ?? 'N/A',
                    'quantity' => $it->quantity ?? 'N/A',
                    'unit'     => $it->unit->unit_name ?? 'N/A',
                    'price'    => $it->price ?? 'N/A',
                ];
            });

        return response()->json(['items' => $items]);
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
            'vendor_id' => 'required|exists:flower__vendor_details,vendor_id',
            'pickup_date' => 'required|date',
            'rider_id' => 'required|exists:flower__rider_details,rider_id',
            'flower_id' => 'required|array',
            'flower_id.*' => 'required|exists:flower_products,product_id',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|exists:pooja_units,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
        ]);
    
        // Generate unique pick_up_id
        $pickUpId = 'PICKUP-' . strtoupper(uniqid());
    
        // Save main flower pickup details
        // Save flower pickup details
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

        $totalPrice = 0; // Initialize total price

        // Save flower items
        foreach ($request->flower_id as $index => $flower_id) {
            $price = $request->price[$index] ?? null; // If no price provided, default to null
            $quantity = $request->quantity[$index];

            // Create FlowerPickupItem
            FlowerPickupItems::create([
                'pick_up_id' => $pickUpId,
                'flower_id' => $flower_id,
                'unit_id' => $request->unit_id[$index],
                'quantity' => $quantity,
                'price' => $price, // Save price as null if not provided
            ]);

            // Add price to total if price is given
            if ($price !== null) {
                $totalPrice += $price ; // Multiply price by quantity and add to total
            }
        }

        // Update total price in FlowerPickupDetails
        $pickup->total_price = $totalPrice;
        $pickup->save();
    
        return redirect()->back()->with('success', 'Flower pickup details saved successfully!');
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
