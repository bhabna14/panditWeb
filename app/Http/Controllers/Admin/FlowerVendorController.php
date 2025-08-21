<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerVendor;
use App\Models\FlowerVendorBank;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerProduct;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class FlowerVendorController extends Controller
{

    public function addVendorDetails()
    {
        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            // ->where('status', 'active') // uncomment if you only want active items
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']);

        return view('admin/add-flower-vendors', compact('flowers'));
    }

public function saveVendorDetails(Request $request)
{
    DB::beginTransaction();

    try {
        // Normalize flower ids
        $incomingFlowerIds = collect($request->input('flower_ids', []))
            ->filter()
            ->map(fn($id) => strtoupper(trim($id)))
            ->unique()
            ->values();

        // Only valid flower products
        $validFlowerIds = FlowerProduct::whereIn('product_id', $incomingFlowerIds)
            ->whereIn('category', ['Flower', 'flower'])
            ->pluck('product_id')
            ->values();

        // Create vendor
        $vendor = FlowerVendor::create([
            'vendor_id'       => 'VENDOR-' . Str::upper(Str::random(10)),
            'vendor_name'     => $request->vendor_name,
            'phone_no'        => $request->phone_no,
            'email_id'        => $request->email_id,
            'vendor_category' => $request->vendor_category,
            'payment_type'    => $request->payment_type,
            'vendor_gst'      => $request->vendor_gst,
            'vendor_address'  => $request->vendor_address,
            'flower_ids'      => $validFlowerIds->all(),
        ]);

        // Save bank rows
        $rows = max(
            count($request->input('bank_name', [])),
            count($request->input('account_no', [])),
            count($request->input('ifsc_code', [])),
            count($request->input('upi_id', []))
        );

        for ($i = 0; $i < $rows; $i++) {
            $bankName  = trim($request->bank_name[$i]  ?? '');
            $accountNo = trim($request->account_no[$i] ?? '');
            $ifscCode  = strtoupper(trim($request->ifsc_code[$i]  ?? ''));
            $upiId     = trim($request->upi_id[$i]     ?? '');

            if ($bankName || $accountNo || $ifscCode || $upiId) {
                FlowerVendorBank::create([
                    'vendor_id'  => $vendor->vendor_id,
                    'bank_name'  => $bankName ?: null,
                    'account_no' => $accountNo ?: null,
                    'ifsc_code'  => $ifscCode ?: null,
                    'upi_id'     => $upiId ?: null,
                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('admin.addvendor') // ⚠️ ensure this route exists
            ->with('success', 'Vendor details saved successfully.');

    } catch (\Throwable $e) {
        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with('error', "Error: {$e->getMessage()} in {$e->getFile()} line {$e->getLine()}");
    }
}

    public function manageVendorDetails()
    {
        // Active vendors + banks
        $vendor_details = FlowerVendor::where('status', 'active')
            ->with('vendorBanks')
            ->get();

        // All flower products (category = Flower) for checkbox list in modal
        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']);

        return view('admin.manage-flower-vendors', compact('vendor_details', 'flowers'));
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
