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
use Illuminate\Validation\Rule;
use Carbon\Carbon;


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

// public function saveVendorDetails(Request $request)
// {
//     // Validate request (date_of_joining required to match Blade)
//     $validated = $request->validate([
//         'vendor_name'     => 'required|string|max:255',
//         'phone_no'        => ['required','string','regex:/^\+?[0-9]{7,15}$/'],
//         'vendor_category' => 'required|string|max:255',
//         'email_id'        => 'nullable|email|max:255',
//         'payment_type'    => 'nullable|in:UPI,Bank,Cash',
//         'vendor_gst'      => 'nullable|string|max:20',
//         'vendor_address'  => 'nullable|string|max:500',

//         'flower_ids'      => 'nullable|array',
//         'flower_ids.*'    => 'integer',

//         'date_of_joining' => 'required|date',

//         // Only PDFs per UI
//         'vendor_document' => 'nullable|file|mimes:pdf|max:5120',

//         // Bank details
//         'bank_name'       => 'nullable|array',
//         'bank_name.*'     => 'nullable|string|max:255',
//         'account_no'      => 'nullable|array',
//         'account_no.*'    => 'nullable|regex:/^[0-9]{9,20}$/',
//         'ifsc_code'       => 'nullable|array',
//         'ifsc_code.*'     => 'nullable|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
//         'upi_id'          => 'nullable|array',
//         'upi_id.*'        => 'nullable|regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/',
//     ], [
//         'phone_no.regex' => 'Phone must be 7–15 digits (optional leading +).',
//         'account_no.*.regex' => 'Account number must be 9–20 digits.',
//         'ifsc_code.*.regex'  => 'IFSC format should be AAAA0XXXXXX.',
//         'upi_id.*.regex'     => 'UPI should look like name@bank.',
//     ]);

//     DB::beginTransaction();

//     try {
//         // Create vendor
//         $vendor = new FlowerVendor();
//         $vendor->vendor_id       = (string) \Str::uuid();
//         $vendor->vendor_name     = $validated['vendor_name'];
//         $vendor->phone_no        = $validated['phone_no'];
//         $vendor->email_id        = $validated['email_id'] ?? null;
//         $vendor->vendor_category = $validated['vendor_category'];
//         $vendor->payment_type    = $validated['payment_type'] ?? null;
//         $vendor->vendor_gst      = $validated['vendor_gst'] ?? null;
//         $vendor->vendor_address  = $validated['vendor_address'] ?? null;
//         $vendor->flower_ids      = $validated['flower_ids'] ?? [];
//         $vendor->date_of_joining = $validated['date_of_joining'];

//         // Store the document on "public" disk -> accessible via Storage::url()
//         if ($request->hasFile('vendor_document')) {
//             $vendor->vendor_document = $request->file('vendor_document')
//                                               ->store('vendor_documents', 'public');
//         }

//         $vendor->save();

//         // Save multiple bank details (skip completely empty rows)
//         $bankNames   = $request->input('bank_name', []);
//         $accountNos  = $request->input('account_no', []);
//         $ifscCodes   = $request->input('ifsc_code', []);
//         $upiIds      = $request->input('upi_id', []);

//         $rows = max(
//             count($bankNames),
//             count($accountNos),
//             count($ifscCodes),
//             count($upiIds)
//         );

//         for ($i = 0; $i < $rows; $i++) {
//             $bankName  = isset($bankNames[$i])  ? trim($bankNames[$i])  : null;
//             $accountNo = isset($accountNos[$i]) ? trim($accountNos[$i]) : null;
//             $ifsc      = isset($ifscCodes[$i])  ? trim(strtoupper($ifscCodes[$i]))  : null;
//             $upi       = isset($upiIds[$i])     ? trim($upiIds[$i])     : null;

//             if ($bankName || $accountNo || $ifsc || $upi) {
//                 FlowerVendorBank::create([
//                     'vendor_id'  => $vendor->vendor_id,
//                     'bank_name'  => $bankName ?: null,
//                     'account_no' => $accountNo ?: null,
//                     'ifsc_code'  => $ifsc ?: null,
//                     'upi_id'     => $upi ?: null,
//                 ]);
//             }
//         }

//         DB::commit();
//         return redirect()->back()->with('success', 'Vendor details saved successfully!');
//     } catch (\Throwable $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Failed to save vendor. '.$e->getMessage());
//     }
// }

    public function saveVendorDetails(Request $request)
    {
        // ---- Validate request ----
        $validated = $request->validate([
            'vendor_name'      => ['required', 'string', 'max:255'],
            'phone_no'         => [
                'required',
                'regex:/^\+?[0-9]{7,15}$/',
                Rule::unique('flower__vendor_details', 'phone_no'),
            ],
            'email_id'         => ['nullable', 'email', 'max:255', Rule::unique('flower__vendor_details', 'email_id')],
            'vendor_category'  => ['required', Rule::in(['farmer', 'retailer', 'dealer'])],
            'payment_type'     => ['nullable', Rule::in(['UPI', 'Bank', 'Cash'])],
            'vendor_gst'       => ['nullable'],
            'vendor_address'   => ['nullable', 'string', 'max:1000'],
            'date_of_joining'  => ['required', 'date', 'before_or_equal:today'],
            'vendor_document'  => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5 MB

            // Flower selections
            'flower_ids'       => ['nullable', 'array'],
            'flower_ids.*'     => ['integer'],

            // Bank arrays (rows are optional; we’ll skip completely empty ones)
            'bank_name'        => ['nullable', 'array'],
            'bank_name.*'      => ['nullable', 'string', 'max:255'],
            'account_no'       => ['nullable', 'array'],
            'account_no.*'     => ['nullable', 'regex:/^[0-9]{9,20}$/'],
            'ifsc_code'        => ['nullable', 'array'],
            'ifsc_code.*'      => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi_id'           => ['nullable', 'array'],
            'upi_id.*'         => ['nullable', 'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/'],
        ], [
            'phone_no.regex'   => 'Phone must be 7–15 digits (optional leading +).',
            'vendor_gst.regex' => 'GST format looks invalid.',
            'account_no.*.regex' => 'Account number must be 9–20 digits.',
            'ifsc_code.*.regex'  => 'IFSC format must be AAAA0XXXXXX.',
            'upi_id.*.regex'     => 'UPI ID format looks invalid (e.g., name@bank).',
        ]);

        try {
            DB::beginTransaction();

            // ---- Generate a unique string vendor_id ----
            // Example: VND-20250826-AB12CD
            do {
                $candidate = 'VND-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            } while (FlowerVendor::where('vendor_id', $candidate)->exists());

            // ---- Upload optional PDF ----
            $docPath = null;
            if ($request->hasFile('vendor_document')) {
                // Ensure `php artisan storage:link` so Storage::url() works.
                $docPath = $request->file('vendor_document')->store('vendor_docs', 'public');
            }

            // ---- Create vendor ----
            $vendor = FlowerVendor::create([
                'vendor_id'       => $candidate,
                'vendor_name'     => $validated['vendor_name'],
                'phone_no'        => $validated['phone_no'],
                'email_id'        => $validated['email_id'] ?? null,
                'vendor_category' => $validated['vendor_category'],
                'payment_type'    => $validated['payment_type'] ?? null,
                'vendor_gst'      => isset($validated['vendor_gst']) ? strtoupper($validated['vendor_gst']) : null,
                'vendor_address'  => $validated['vendor_address'] ?? null,
                'flower_ids'      => $request->input('flower_ids', []), // casted to array in model
                'date_of_joining' => $validated['date_of_joining'],
                'vendor_document' => $docPath, // store relative path; use Storage::url() when displaying
            ]);

            // ---- Create related bank rows (skip empty rows) ----
            $bankNames   = $request->input('bank_name', []);
            $accountNos  = $request->input('account_no', []);
            $ifscCodes   = $request->input('ifsc_code', []);
            $upiIds      = $request->input('upi_id', []);

            $max = max(
                count($bankNames),
                count($accountNos),
                count($ifscCodes),
                count($upiIds)
            );

            for ($i = 0; $i < $max; $i++) {
                $bankName  = trim($bankNames[$i]  ?? '');
                $acctNo    = trim($accountNos[$i]  ?? '');
                $ifsc      = strtoupper(trim($ifscCodes[$i] ?? ''));
                $upi       = trim($upiIds[$i]      ?? '');

                // skip a row if *all* fields are empty
                if ($bankName === '' && $acctNo === '' && $ifsc === '' && $upi === '') {
                    continue;
                }

                FlowerVendorBank::create([
                    'temple_id'  => optional(auth()->user())->temple_id, // adjust if you keep temple scoping
                    'vendor_id'  => $vendor->vendor_id,
                    'bank_name'  => $bankName ?: null,
                    'account_no' => $acctNo   ?: null,
                    'ifsc_code'  => $ifsc     ?: null,
                    'upi_id'     => $upi      ?: null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.managevendor')
                ->with('success', 'Vendor created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to save vendor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // If a file was uploaded and we saved it, you may optionally remove it on failure.
            if (!empty($docPath)) {
                Storage::disk('public')->delete($docPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the vendor. Please try again.');
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
