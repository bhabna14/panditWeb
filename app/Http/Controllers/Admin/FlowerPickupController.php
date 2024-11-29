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
        foreach ($request->flower_id as $index => $flower_id) {
            FlowerPickupDetails::create([
                'flower_id' => $flower_id,
                'unit_id' => $request->unit_id[$index],
                'quantity' => $request->quantity[$index],
                'vendor_id' => $request->vendor_id[$index],
                'rider_id' => $request->rider_id[$index],
                'pickup_date' => $request->pickup_date[$index],
                'status' => 'active'
            ]);
        }

        return redirect()->back()->with('success', 'Flower pickup details saved successfully!');
    }

}
