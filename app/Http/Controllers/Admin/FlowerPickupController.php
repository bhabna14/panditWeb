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
    //
   
    
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
        try {
            // Get the filter parameter from the URL (if available)
            $filter = $request->input('filter', 'all'); // Default is 'all'
            
            // Build the query
            $query = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider']);
            
            // Apply filters based on the filter parameter
            if ($filter == 'todayexpenses') {
                $query->whereDate('pickup_date', Carbon::today()); // Today's date
            } elseif ($filter == 'todaypaidpickup') {
                $query->whereDate('pickup_date', Carbon::today())
                    ->where('payment_status', 'Paid'); // Today's paid pickups
            } elseif ($filter == 'todaypendingpickup') {
                $query->whereDate('pickup_date', Carbon::today())
                    ->where('payment_status', 'pending'); // Today's pending pickups
            } elseif ($filter == 'monthlyexpenses') {
                $query->whereMonth('pickup_date', Carbon::now()->month); // Current month
            } elseif ($filter == 'monthlypaidpickup') {
                $query->whereMonth('pickup_date', Carbon::now()->month)
                    ->where('payment_status', 'Paid'); // Monthly paid pickups
            } elseif ($filter == 'monthlypendingpickup') {
                $query->whereMonth('pickup_date', Carbon::now()->month)
                    ->where('payment_status', 'pending'); // Monthly pending pickups
            }

            // Order the results by pickup_date in descending order
            $query->orderBy('pickup_date', 'desc');

            // Get the pickup details
            $pickupDetails = $query->get()->groupBy('pickup_date'); // Group by pickup date
            
            // Calculate total expenses for today
            $totalExpensesday = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())->sum('total_price'); 

            // Return the view with the filtered data
            return view('admin.flower-pickup-details.manage-flower-pickup-details', compact('pickupDetails', 'totalExpensesday'));
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to fetch pickup details: ' . $e->getMessage()]);
        }
    }


    
    // public function edit($id)
    // {
    //     // Fetch the specific record with required relationships
    //     $pickupDetail = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
    //         ->findOrFail($id);
    
    //     // Fetch all available flowers (if dropdown is needed)
    //     $flowers = FlowerProduct::where('status', 'active')
    //                     ->where('category', 'Flower')
    //                     ->get();
    //                     $units = PoojaUnit::where('status', 'active')->get();
    //     // Pass the data to the view
    //     return view('admin.flower-pickup-details.edit-flower-pickup-details', compact('pickupDetail', 'flowers','units'));
    // }
    
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
            'status' => 'PickupCompleted',
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
    
//     public function update(Request $request, $id)
// {
//     $pickupDetail = FlowerPickupDetails::findOrFail($id);

//     // Update main details
//     $pickupDetail->update([
//         'vendor_id' => $request->vendor_id,
//         'rider_id' => $request->rider_id,
//         'pickup_date' => $request->pickup_date,
//     ]);

//     // Update flower pickup items
//     foreach ($request->flowers as $itemId => $data) {
//         FlowerPickupItems::where('id', $itemId)->update([
//             'quantity' => $data['quantity'],
//             'price' => $data['price'],
//         ]);
//     }

//     return redirect()->route('admin.manageflowerpickupdetails')->with('success', 'Pickup details updated successfully!');
// }
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
    $pickupDetail->save();

    // Log the payment update
    Log::info('Payment updated', [
        'pickup_id' => $pickup_id,
        'payment_method' => $request->input('payment_method'),
        'payment_id' => $request->input('payment_id')
    ]);

    return redirect()->back()->with('success', 'Payment details updated successfully');
}



}
