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
use Illuminate\Support\Facades\Storage;

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
        // Validate request
        $validated = $request->validate([
            'vendor_name'     => 'required|string|max:255',
            'phone_no'        => 'required|string|max:20',
            'vendor_category' => 'required|string|max:255',
            'email_id'        => 'nullable|email|max:255',
            'payment_type'    => 'nullable|in:UPI,Bank,Cash',
            'vendor_gst'      => 'nullable|string|max:20',
            'vendor_address'  => 'nullable|string|max:500',

            'flower_ids'      => 'nullable|array',
            'flower_ids.*'    => 'nullable|string',

            'date_of_joining' => 'nullable|date',

            // Store only PDFs (adjust if you really want images too)
            'vendor_document' => 'nullable|file|mimes:pdf|max:5120',

            // Bank details
            'bank_name'       => 'nullable|array',
            'bank_name.*'     => 'nullable|string|max:255',
            'account_no'      => 'nullable|array',
            'account_no.*'    => 'nullable|string|max:32',
            'ifsc_code'       => 'nullable|array',
            'ifsc_code.*'     => 'nullable|string|max:15',
            'upi_id'          => 'nullable|array',
            'upi_id.*'        => 'nullable|string|max:64',
        ]);

        DB::beginTransaction();

        try {
            // Create vendor
            $vendor = new FlowerVendor();
            $vendor->vendor_id       = (string) \Str::uuid();
            $vendor->vendor_name     = $validated['vendor_name'];
            $vendor->phone_no        = $validated['phone_no'];
            $vendor->email_id        = $validated['email_id'] ?? null;
            $vendor->vendor_category = $validated['vendor_category'];
            $vendor->payment_type    = $validated['payment_type'] ?? null;
            $vendor->vendor_gst      = $validated['vendor_gst'] ?? null;
            $vendor->vendor_address  = $validated['vendor_address'] ?? null;
            $vendor->flower_ids      = $validated['flower_ids'] ?? [];
            $vendor->date_of_joining = $validated['date_of_joining'] ?? null;

            // Store the document on the "public" disk so it is web-accessible via /storage symlink
            if ($request->hasFile('vendor_document')) {
                $file     = $request->file('vendor_document');
                // Use hashed name to avoid collisions. Directory kept concise.
                $path     = $file->store('vendor_documents', 'public'); // e.g. vendor_documents/AbCdEf.pdf
                $vendor->vendor_document = $path;  // Save the relative path (Storage::url will build the URL)
            }

            $vendor->save();

            // Save multiple bank details (skip completely empty rows)
            $bankNames   = $request->input('bank_name', []);
            $accountNos  = $request->input('account_no', []);
            $ifscCodes   = $request->input('ifsc_code', []);
            $upiIds      = $request->input('upi_id', []);

            $rows = max(
                count($bankNames),
                count($accountNos),
                count($ifscCodes),
                count($upiIds)
            );

            for ($i = 0; $i < $rows; $i++) {
                $bankName  = isset($bankNames[$i])  ? trim($bankNames[$i])  : null;
                $accountNo = isset($accountNos[$i]) ? trim($accountNos[$i]) : null;
                $ifsc      = isset($ifscCodes[$i])  ? trim($ifscCodes[$i])  : null;
                $upi       = isset($upiIds[$i])     ? trim($upiIds[$i])     : null;

                // If the row has any meaningful data, store it
                if ($bankName || $accountNo || $upi || $ifsc) {
                    FlowerVendorBank::create([
                        'vendor_id'  => $vendor->vendor_id,
                        'bank_name'  => $bankName ?: null,
                        'account_no' => $accountNo ?: null,
                        'ifsc_code'  => $ifsc ?: null,
                        'upi_id'     => $upi ?: null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Vendor details saved successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save vendor. '.$e->getMessage());
        }
    }
        
    public function manageVendorDetails()
    {
        // Active vendors + banks
        $vendor_details = FlowerVendor::where('status', 'active')
            ->with('vendorBanks') // ensure relation exists: FlowerVendor hasMany FlowerVendorBank
            ->orderBy('vendor_name')
            ->get();

        // All flower products (category = Flower) for client-side lookup in modal
        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']);

        return view('admin.manage-vendors', compact('vendor_details', 'flowers'));
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
        
    public function editVendorDetails($vendor_id)
    {
        $vendordetails = FlowerVendor::with('vendorBanks')->findOrFail($vendor_id);

        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']);

        return view('admin.edit-flower-vendor', compact('vendordetails', 'flowers'));
    }
        
    public function updateVendorDetails(Request $request, $vendor_id)
    {
        $validated = $request->validate([
            'vendor_name'     => 'required|string|max:255',
            'phone_no'        => 'required|string|max:20',
            'vendor_category' => 'required|string|max:255',
            'email_id'        => 'nullable|email|max:255',
            'payment_type'    => 'nullable|in:UPI,Bank,Cash',
            'vendor_gst'      => 'nullable|string|max:20',
            'vendor_address'  => 'nullable|string|max:500',

            'flower_ids'      => 'nullable|array',
            'flower_ids.*'    => 'nullable|string',

            'date_of_joining' => 'nullable|date',

            // Match your UI (PDF only). If you want images too, add: ,jpg,jpeg,png
            'vendor_document' => 'nullable|file|mimes:pdf|max:5120',

            // Banks
            'bank_id'    => 'nullable|array',
            'bank_name'  => 'nullable|array',
            'account_no' => 'nullable|array',
            'ifsc_code'  => 'nullable|array',
            'upi_id'     => 'nullable|array',
        ]);

        $vendor = FlowerVendor::findOrFail($vendor_id);

        DB::beginTransaction();

        try {
            // Update core fields
            $vendor->vendor_name     = $validated['vendor_name'];
            $vendor->phone_no        = $validated['phone_no'];
            $vendor->email_id        = $validated['email_id'] ?? null;
            $vendor->vendor_category = $validated['vendor_category'];
            $vendor->payment_type    = $validated['payment_type'] ?? null;
            $vendor->vendor_gst      = $validated['vendor_gst'] ?? null;
            $vendor->vendor_address  = $validated['vendor_address'] ?? null;
            $vendor->flower_ids      = $validated['flower_ids'] ?? [];
            $vendor->date_of_joining = $validated['date_of_joining'] ?? null;

            // Replace document (public disk), delete old if present
            if ($request->hasFile('vendor_document')) {
                if ($vendor->vendor_document && Storage::disk('public')->exists($vendor->vendor_document)) {
                    Storage::disk('public')->delete($vendor->vendor_document);
                }
                $vendor->vendor_document = $request->file('vendor_document')
                                                ->store('vendor_documents', 'public'); // e.g. vendor_documents/abc.pdf
            }

            $vendor->save();

            // ----- Banks -----
            $bankIds    = $request->input('bank_id', []);
            $bankNames  = $request->input('bank_name', []);
            $accountNos = $request->input('account_no', []);
            $ifscCodes  = $request->input('ifsc_code', []);
            $upiIds     = $request->input('upi_id', []);

            $rows = max(
                count($bankIds),
                count($bankNames),
                count($accountNos),
                count($ifscCodes),
                count($upiIds)
            );

            $updatedBankIds = [];

            for ($i = 0; $i < $rows; $i++) {
                $id   = $bankIds[$i]    ?? null;
                $name = trim($bankNames[$i]  ?? '');
                $acc  = trim($accountNos[$i] ?? '');
                $ifsc = trim($ifscCodes[$i]  ?? '');
                $upi  = trim($upiIds[$i]     ?? '');

                // Skip fully empty row
                if ($name === '' && $acc === '' && $ifsc === '' && $upi === '') {
                    continue;
                }

                if ($id) {
                    // Update existing bank (guard by vendor_id)
                    $bank = FlowerVendorBank::where('id', $id)
                        ->where('vendor_id', $vendor->vendor_id)
                        ->first();

                    if ($bank) {
                        $bank->update([
                            'bank_name'  => $name ?: null,
                            'account_no' => $acc ?: null,
                            'ifsc_code'  => $ifsc ?: null,
                            'upi_id'     => $upi ?: null,
                        ]);
                        $updatedBankIds[] = $bank->id;
                    }
                } else {
                    // Create new bank
                    $bank = FlowerVendorBank::create([
                        'vendor_id'  => $vendor->vendor_id,
                        'bank_name'  => $name ?: null,
                        'account_no' => $acc ?: null,
                        'ifsc_code'  => $ifsc ?: null,
                        'upi_id'     => $upi ?: null,
                    ]);
                    $updatedBankIds[] = $bank->id;
                }
            }

            // Remove deleted banks ONLY if we actually processed some bank rows
            if (count($updatedBankIds) > 0) {
                FlowerVendorBank::where('vendor_id', $vendor->vendor_id)
                    ->whereNotIn('id', $updatedBankIds)
                    ->delete();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Vendor details updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update vendor. '.$e->getMessage());
        }
    }

}
