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
use Illuminate\Support\Facades\Log;

class FlowerPickupController extends Controller
{
    //
   
    
    public function addflowerpickupdetails()
    {
      
        $flowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
        $units = PoojaUnit::where('status', 'active')->get();
        $vendors = FlowerVendor::all();
        $riders = RiderDetails::all();
    
        return view('admin.flower-pickup-details.add-flower-pickup-details', compact('flowers', 'units', 'vendors', 'riders'));
    }
    
    public function manageflowerpickupdetails()
    {
        // Fetch all pickup details with their related data
        $pickupDetails = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
            ->get()
            ->groupBy('pickup_date'); // Group by pickup date for easy separation in the view
    
        // Pass the organized data to the view
        return view('admin.flower-pickup-details.manage-flower-pickup-details', compact('pickupDetails'));
    }
    
    public function edit($id)
    {
        // Fetch the specific record with required relationships
        $pickupDetail = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
            ->findOrFail($id);
    
        // Fetch all available flowers (if dropdown is needed)
        $flowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
                        $units = PoojaUnit::where('status', 'active')->get();
        // Pass the data to the view
        return view('admin.flower-pickup-details.edit-flower-pickup-details', compact('pickupDetail', 'flowers','units'));
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
            'payment_id' => null,
        ]);
    
      
        // Save flower items
        foreach ($request->flower_id as $index => $flower_id) {
            FlowerPickupItems::create([
                'pick_up_id' => $pickUpId,
                'flower_id' => $flower_id,
                'unit_id' => $request->unit_id[$index],
                'quantity' => $request->quantity[$index],
                'price' => null, // Set price as null initially
            ]);
        }

    
        return redirect()->back()->with('success', 'Flower pickup details saved successfully!');
    }
    
    public function update(Request $request, $id)
{
    $pickupDetail = FlowerPickupDetails::findOrFail($id);

    // Update main details
    $pickupDetail->update([
        'vendor_id' => $request->vendor_id,
        'rider_id' => $request->rider_id,
        'pickup_date' => $request->pickup_date,
    ]);

    // Update flower pickup items
    foreach ($request->flowers as $itemId => $data) {
        FlowerPickupItems::where('id', $itemId)->update([
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    return redirect()->route('admin.manageflowerpickupdetails')->with('success', 'Pickup details updated successfully!');
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
