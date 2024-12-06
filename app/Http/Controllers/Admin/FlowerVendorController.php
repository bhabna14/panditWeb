<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerVendor;
use App\Models\FlowerVendorBank;
use App\Models\FlowerPickupDetails;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class FlowerVendorController extends Controller
{
    public function addVendorDetails(){
        return view("admin/add-flower-vendors");
    }

    public function saveVendorDetails(Request $request)
{
    $request->validate([
        'vendor_name' => 'required|string|max:255',
        'phone_no' => 'required|string|max:255',
        'vendor_category' => 'required|string|max:255',
        'bank_name.*' => 'nullable|string|max:255',
        'account_no.*' => 'nullable|numeric',
        'ifsc_code.*' => 'nullable|string|max:15',
        'upi_id.*' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        // Save vendor details
        $vendorDetails = new FlowerVendor();
        $vendorDetails->vendor_id = 'VENDOR' . uniqid();
        $vendorDetails->vendor_name = $request->vendor_name;
        $vendorDetails->phone_no = $request->phone_no;
        $vendorDetails->email_id = $request->email_id;
        $vendorDetails->vendor_category = $request->vendor_category;
        $vendorDetails->payment_type = $request->payment_type;
        $vendorDetails->vendor_gst = $request->vendor_gst;
        $vendorDetails->vendor_address = $request->vendor_address;
        $vendorDetails->save();

        // Save bank details
        if (!empty($request->bank_name)) {
            foreach ($request->bank_name as $index => $bankName) {
                if (trim($bankName) || trim($request->account_no[$index]) || trim($request->ifsc_code[$index]) || trim($request->upi_id[$index])) {
                    $vendorBank = new FlowerVendorBank();
                    $vendorBank->vendor_id = $vendorDetails->vendor_id;
                    $vendorBank->bank_name = trim($bankName);
                    $vendorBank->account_no = trim($request->account_no[$index] ?? null);
                    $vendorBank->ifsc_code = trim($request->ifsc_code[$index] ?? null);
                    $vendorBank->upi_id = trim($request->upi_id[$index] ?? null);
                    $vendorBank->save();
                }
            }
        }

        DB::commit();

        session()->flash('success', 'Vendor details saved successfully along with bank details.');
        return redirect()->route('admin.addvendor');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error saving vendor details: ' . $e->getMessage());
        session()->flash('error', 'An error occurred while saving vendor details. Please try again.');
        return redirect()->back()->withInput();
    }
}

    public function manageVendorDetails()
    {    
        // Fetch active vendors with related bank details
        $vendor_details = FlowerVendor::where('status', 'active')
                            ->with('vendorBanks') 
                            ->get();
    
        return view('admin.manage-flower-vendors', compact('vendor_details'));
    }

    public function vendorAllDetails($id){

        $pickupDetails = FlowerPickupDetails::with(['flowerPickupItems.flower', 'flowerPickupItems.unit', 'vendor', 'rider'])
        ->where('vendor_id', $id)
        ->get()
        ->groupBy('pickup_date');
    
    
    return view('admin.vendor-all-details', compact('pickupDetails'));
    
    }

    public function deleteVendorDetails($id)
    {
    // Find the vendor by ID
    $vendor = FlowerVendor::find($id);
    
    if ($vendor) {
        // Start a database transaction
        \DB::beginTransaction();

        try {
            // Update the status of the vendor to 'deleted'
            $vendor->status = 'deleted';
            $vendor->save();

            // Retrieve all related bank records and update their status to 'deleted'
            $vendorBanks = $vendor->vendorBanks; // Using the relationship defined in the VendorDetails model

            foreach ($vendorBanks as $bank) {
                $bank->status = 'deleted';
                $bank->save();
            }

            // Commit the transaction
            \DB::commit();

            return redirect()->back()->with('success', 'Vendor and associated bank details deleted.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            \DB::rollback();

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    } else {
        return redirect()->back()->with('error', 'Vendor not found.');
    }
    }

    public function editVendorDetails($id)
    {
        $vendordetails = FlowerVendor::with('vendorBanks')->findOrFail($id);
        return view('admin.edit-flower-vendor', compact('vendordetails'));
    }


    public function updateVendorDetails(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'phone_no' => 'required|numeric',
            'email_id' => 'nullable|email',
            'vendor_category' => 'required|string|max:255',
            'payment_type' => 'nullable|string|max:255',
            'vendor_gst' => 'nullable|string|max:15',
            'vendor_address' => 'nullable|string|max:255',
            'bank_name.*' => 'nullable|string|max:255',
            'account_no.*' => 'nullable|numeric',
            'ifsc_code.*' => 'nullable|string|max:11',
            'upi_id.*' => 'nullable|string|max:255',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Update vendor details
            $vendor = FlowerVendor::findOrFail($id);
            $vendor->update($validatedData);
    
            // Handle bank details
            $bankIds = $request->bank_id ?? [];
            foreach ($bankIds as $index => $bankId) {
                $bankData = [
                    'bank_name' => $request->bank_name[$index] ?? null,
                    'account_no' => $request->account_no[$index] ?? null,
                    'ifsc_code' => $request->ifsc_code[$index] ?? null,
                    'upi_id' => $request->upi_id[$index] ?? null,
                ];
    
                if ($bankId) {
                    // Update existing bank detail
                    $vendor->vendorBanks()->where('id', $bankId)->update($bankData);
                } else {
                    // Create a new bank detail
                    $vendor->vendorBanks()->create($bankData);
                }
            }
    
            DB::commit();
    
            return redirect()->route('admin.managevendor')->with('success', 'Vendor details updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->back()->with('error', 'An error occurred while saving vendor details. Please try again.');
        }
    }
}
