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
use Illuminate\Validation\Rule;
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
        try {
            // Validate
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

                'bank_name'       => 'nullable|array',
                'bank_name.*'     => 'nullable|string|max:255',
                'account_no'      => 'nullable|array',
                'account_no.*'    => 'nullable|string|max:32',
                'ifsc_code'       => 'nullable|array',
                'ifsc_code.*'     => 'nullable|string|max:15',
                'upi_id'          => 'nullable|array',
                'upi_id.*'        => 'nullable|string|max:64',

                'date_of_joining' => 'nullable|date',
                // PDF or image (jpg/jpeg/png) up to 5 MB
                'vendor_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            // Everything important happens inside a transaction so the code is unique even under load
            $vendor = DB::transaction(function () use ($validated, $request) {

                // ===== Generate next vendor code: VENDOR0001, VENDOR0002, ... =====
                $prefix   = 'VENDOR';
                $startPos = strlen($prefix) + 1; // 7 for "VENDOR0001"

                // Lock matching rows, find the highest numeric suffix (MySQL syntax)
                $lastNum = DB::table('flower__vendor_details')
                    ->where('vendor_id', 'LIKE', $prefix.'%')
                    ->lockForUpdate()
                    ->select(DB::raw('MAX(CAST(SUBSTRING(vendor_id, '.$startPos.') AS UNSIGNED)) AS max_num'))
                    ->value('max_num');

                $next = (int)$lastNum + 1; // if null, becomes 1

                $newVendorId = $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);

                // ===== Create vendor row =====
                $vendor = new FlowerVendor();
                $vendor->vendor_id        = $newVendorId; // primary key
                $vendor->vendor_name      = $validated['vendor_name'];
                $vendor->phone_no         = $validated['phone_no'];
                $vendor->email_id         = $validated['email_id'] ?? null;
                $vendor->vendor_category  = $validated['vendor_category'];
                $vendor->payment_type     = $validated['payment_type'] ?? null;
                $vendor->vendor_gst       = $validated['vendor_gst'] ?? null;
                $vendor->vendor_address   = $validated['vendor_address'] ?? null;
                $vendor->flower_ids       = $validated['flower_ids'] ?? [];
                $vendor->date_of_joining  = $validated['date_of_joining'] ?? null;
                $vendor->vendor_document  = null;

                // Handle file upload (optional)
                if ($request->hasFile('vendor_document')) {
                    $file     = $request->file('vendor_document');
                    $ext      = strtolower($file->getClientOriginalExtension()); // pdf|jpg|jpeg|png
                    $fileName = $newVendorId . '-' . time() . '.' . $ext;
                    $path     = $file->storeAs('vendor_docs', $fileName, 'public'); // storage/app/public/vendor_docs/...
                    $vendor->vendor_document = $path;
                }

                $vendor->save();

                // Bank rows (optional)
                if (!empty($validated['bank_name'])) {
                    foreach ($validated['bank_name'] as $i => $bankName) {
                        $hasAny = !empty($bankName)
                            || !empty($validated['account_no'][$i] ?? null)
                            || !empty($validated['upi_id'][$i] ?? null);

                        if ($hasAny) {
                            FlowerVendorBank::create([
                                'vendor_id'  => $vendor->vendor_id,
                                'bank_name'  => $bankName,
                                'account_no' => $validated['account_no'][$i] ?? null,
                                'ifsc_code'  => $validated['ifsc_code'][$i] ?? null,
                                'upi_id'     => $validated['upi_id'][$i] ?? null,
                            ]);
                        }
                    }
                }

                return $vendor;
            });

            return back()->with('success', 'Vendor saved! ID: '.$vendor->vendor_id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to save vendor. '.$e->getMessage());
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

    $totalVendors   = $vendor_details->count();
    $activeVendors  = $vendor_details->where('status', 'active')->count();
    $inactiveVendors= $vendor_details->where('status', 'inactive')->count();

        return view('admin.manage-flower-vendors', compact('vendor_details', 'flowers','totalVendors','activeVendors','inactiveVendors'));
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

    public function updateVendorDetails(Request $request, $vendorId)
    {
        try {
            $vendor = FlowerVendor::with('vendorBanks')->findOrFail($vendorId);

            $validated = $request->validate([
                'vendor_name'     => 'required|string|max:255',
                'phone_no'        => 'required|string|max:20',
                'vendor_category' => 'required|in:Farmer,Retailer,Dealer',
                'email_id'        => 'nullable|email|max:255',
                'payment_type'    => 'nullable|in:UPI,Bank,Cash',
                'vendor_gst'      => 'nullable|string|max:20',
                'vendor_address'  => 'nullable|string|max:500',

                'flower_ids'      => 'nullable|array',
                'flower_ids.*'    => 'nullable|string',

                'date_of_joining' => 'nullable|date',

                // optional new file
                'vendor_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

                // bank arrays (all optional)
                'bank_id'         => 'nullable|array',
                'bank_id.*'       => 'nullable|integer',
                'bank_name'       => 'nullable|array',
                'bank_name.*'     => 'nullable|string|max:255',
                'account_no'      => 'nullable|array',
                'account_no.*'    => 'nullable|string|max:32',
                'ifsc_code'       => 'nullable|array',
                'ifsc_code.*'     => 'nullable|string|max:15',
                'upi_id'          => 'nullable|array',
                'upi_id.*'        => 'nullable|string|max:64',
            ]);

            // Update simple fields
            $vendor->vendor_name     = $validated['vendor_name'];
            $vendor->phone_no        = $validated['phone_no'];
            $vendor->email_id        = $validated['email_id'] ?? null;
            $vendor->vendor_category = $validated['vendor_category'];
            $vendor->payment_type    = $validated['payment_type'] ?? null;
            $vendor->vendor_gst      = $validated['vendor_gst'] ?? null;
            $vendor->vendor_address  = $validated['vendor_address'] ?? null;
            $vendor->flower_ids      = $validated['flower_ids'] ?? [];
            $vendor->date_of_joining = $validated['date_of_joining'] ?? null;

            // Handle file replacement
            if ($request->hasFile('vendor_document')) {
                // delete old file if present
                if ($vendor->vendor_document && Storage::disk('public')->exists($vendor->vendor_document)) {
                    Storage::disk('public')->delete($vendor->vendor_document);
                }

                $file      = $request->file('vendor_document');
                $ext       = strtolower($file->getClientOriginalExtension()); // pdf|jpg|jpeg|png
                $fileName  = $vendor->vendor_id . '-' . time() . '.' . $ext;
                $stored    = $file->storeAs('vendor_docs', $fileName, 'public'); // storage/app/public/vendor_docs/...
                $vendor->vendor_document = $stored; // e.g., vendor_docs/VENDOR0007-169321....pdf
            }

            DB::transaction(function () use ($vendor, $validated) {
                $vendor->save();

                // ----- Sync bank rows -----
                $existingIds = $vendor->vendorBanks->pluck('id')->all();
                $submittedIds = array_filter($validated['bank_id'] ?? [], fn($v) => !is_null($v));
                $keepIds = [];

                // Iterate through submitted rows by index
                $rowsCount = max(
                    count($validated['bank_name'] ?? []),
                    count($validated['account_no'] ?? []),
                    count($validated['ifsc_code'] ?? []),
                    count($validated['upi_id'] ?? []),
                    count($validated['bank_id'] ?? [])
                );

                for ($i = 0; $i < $rowsCount; $i++) {
                    $rowId    = $validated['bank_id'][$i]    ?? null;
                    $bname    = $validated['bank_name'][$i]  ?? null;
                    $acc      = $validated['account_no'][$i] ?? null;
                    $ifsc     = $validated['ifsc_code'][$i]  ?? null;
                    $upi      = $validated['upi_id'][$i]     ?? null;

                    $hasAny = !empty($bname) || !empty($acc) || !empty($ifsc) || !empty($upi);

                    if ($rowId) {
                        // Update existing row if it still has any data
                        if ($hasAny) {
                            FlowerVendorBank::where('id', $rowId)->where('vendor_id', $vendor->vendor_id)->update([
                                'bank_name'  => $bname,
                                'account_no' => $acc,
                                'ifsc_code'  => $ifsc,
                                'upi_id'     => $upi,
                            ]);
                            $keepIds[] = (int)$rowId;
                        } else {
                            // No data -> delete this bank row
                            FlowerVendorBank::where('id', $rowId)->where('vendor_id', $vendor->vendor_id)->delete();
                        }
                    } else {
                        // Create new row if any data entered
                        if ($hasAny) {
                            $new = FlowerVendorBank::create([
                                'vendor_id'  => $vendor->vendor_id,
                                'bank_name'  => $bname,
                                'account_no' => $acc,
                                'ifsc_code'  => $ifsc,
                                'upi_id'     => $upi,
                            ]);
                            $keepIds[] = $new->id;
                        }
                    }
                }

                // Delete any existing rows that were not resubmitted
                $idsToDelete = array_diff($existingIds, $keepIds);
                if (!empty($idsToDelete)) {
                    FlowerVendorBank::where('vendor_id', $vendor->vendor_id)
                        ->whereIn('id', $idsToDelete)
                        ->delete();
                }
            });

            return back()->with('success', 'Vendor details updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to update vendor. '.$e->getMessage());
        }
    }

}
