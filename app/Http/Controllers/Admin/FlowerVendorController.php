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
    // 1) Validate inputs
    $validated = $request->validate([
        'vendor_name'     => 'required|string|max:255',
        'phone_no'        => 'required|string|max:20',
        'vendor_category' => 'required|string|max:255',
        'email_id'        => 'nullable|email|max:255',
        'payment_type'    => 'nullable|in:UPI,Bank,Cash',
        'vendor_gst'      => 'nullable|string|max:20',
        'vendor_address'  => 'nullable|string|max:500',

        // arrays (nullable so empty won't error)
        'bank_name'       => 'nullable|array',
        'bank_name.*'     => 'nullable|string|max:255',
        'account_no'      => 'nullable|array',
        'account_no.*'    => 'nullable|string|max:32',
        'ifsc_code'       => 'nullable|array',
        'ifsc_code.*'     => 'nullable|string|max:15',
        'upi_id'          => 'nullable|array',
        'upi_id.*'        => 'nullable|string|max:64',

        'flower_ids'      => 'nullable|array',
        'flower_ids.*'    => 'string',
    ]);

    DB::beginTransaction();

    try {
        // 2) Normalize flower ids
        $incomingFlowerIds = collect($request->input('flower_ids', []))
            ->filter(fn($id) => !empty($id))
            ->map(fn($id) => strtoupper(trim($id)))
            ->unique()
            ->values();

        // 3) Only valid flower products
        $validFlowerIds = FlowerProduct::whereIn('product_id', $incomingFlowerIds)
            ->whereIn('category', ['Flower', 'flower'])
            ->pluck('product_id')
            ->values();

        // 4) Create vendor
        $vendor = new FlowerVendor();
        $vendor->vendor_id       = 'VENDOR-' . Str::upper(Str::random(10));
        $vendor->temple_id       = $request->temple_id ?? null;
        $vendor->vendor_name     = $validated['vendor_name'];
        $vendor->phone_no        = $validated['phone_no'];
        $vendor->email_id        = $validated['email_id'] ?? null;
        $vendor->vendor_category = $validated['vendor_category'];
        $vendor->payment_type    = $validated['payment_type'] ?? null;
        $vendor->vendor_gst      = $validated['vendor_gst'] ?? null;
        $vendor->vendor_address  = $validated['vendor_address'] ?? null;
        $vendor->flower_ids      = $validFlowerIds->all();
        $vendor->save();

        // 5) Save bank rows
        $bankNames  = $request->input('bank_name', []);
        $accountNos = $request->input('account_no', []);
        $ifscCodes  = $request->input('ifsc_code', []);
        $upiIds     = $request->input('upi_id', []);

        $rows = max(count($bankNames), count($accountNos), count($ifscCodes), count($upiIds));

        for ($i = 0; $i < $rows; $i++) {
            $bankName  = trim($bankNames[$i]  ?? '');
            $accountNo = trim($accountNos[$i] ?? '');
            $ifscCode  = strtoupper(trim($ifscCodes[$i]  ?? ''));
            $upiId     = trim($upiIds[$i]     ?? '');

            if ($bankName === '' && $accountNo === '' && $ifscCode === '' && $upiId === '') {
                continue;
            }

            FlowerVendorBank::create([
                'vendor_id'  => $vendor->vendor_id,
                'bank_name'  => $bankName ?: null,
                'account_no' => $accountNo ?: null,
                'ifsc_code'  => $ifscCode ?: null,
                'upi_id'     => $upiId ?: null,
            ]);
        }

        DB::commit();

        return redirect()
            ->route('admin.addvendor')
            ->with('success', 'Vendor details saved successfully along with bank details.');

    } catch (\Throwable $e) {
        DB::rollBack();

        // Log full error
        Log::error('Error saving vendor details', [
            'error' => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return real error to UI (for debugging)
        return redirect()
            ->back()
            ->withInput()
            ->with('error', "Failed to save vendor: " . $e->getMessage() . " (File: " . $e->getFile() . " Line: " . $e->getLine() . ")");
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
