<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use App\Models\FlowerVendor;
use App\Models\RiderDetails;
use App\Models\FlowerPickupDetails;

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
        $pickupDetails = FlowerPickupDetails::with(['flower', 'unit', 'vendor', 'rider'])
            ->orderBy('pickup_date', 'desc')
            ->get()
            ->groupBy('pickup_date'); // Group by date
    // dd($pickupDetails);
        return view('admin.flower-pickup-details.manage-flower-pickup-details', compact('pickupDetails'));
    }
    
    
    public function saveFlowerPickupDetails(Request $request)
    {
        // Validate input to ensure all arrays have matching lengths
        $request->validate([
            'vendor_id' => 'required|array',
            'vendor_id.*' => 'required|exists:flower__vendor_details,vendor_id', // Validate each vendor
            'pickup_date' => 'required|array',
            'pickup_date.*' => 'required|date',
            'flower_id' => 'required|array',
            'flower_id.*' => 'required|exists:flower_products,product_id', // Validate each flower
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|exists:pooja_units,id', // Validate each unit
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'rider_id' => 'required|array',
            'rider_id.*' => 'required|exists:flower__rider_details,rider_id', // Validate each rider
        ]);
    
        // Iterate over the submitted data
        foreach ($request->flower_id as $index => $flower_id) {
            // Check if all required arrays have a value at the current index
            if (
                isset($request->vendor_id[$index], $request->pickup_date[$index], 
                      $request->unit_id[$index], $request->quantity[$index], 
                      $request->rider_id[$index])
            ) {
                FlowerPickupDetails::create([
                    'flower_id' => $flower_id,
                    'unit_id' => $request->unit_id[$index],
                    'quantity' => $request->quantity[$index],
                    'vendor_id' => $request->vendor_id[$index],
                    'rider_id' => $request->rider_id[$index],
                    'pickup_date' => $request->pickup_date[$index],
                    'status' => 'pending',
                ]);
            } else {
                // Log or handle the missing index issue
                return redirect()->back()->withErrors(['error' => 'Mismatched data arrays. Ensure all fields are filled out.']);
            }
        }
    
        return redirect()->back()->with('success', 'Flower pickup details saved successfully!');
    }
    
    

}
