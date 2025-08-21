<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\FlowerVendor;
use App\Models\FlowerProduct;      // adjust namespace/table if different
use App\Models\PoojaUnit;
use App\Models\MonthWiseFlowerPrice;

class MonthWiseFlowerPriceController extends Controller
{
    public function create()
    {
        // Active vendors with their flower_ids
        $vendors = FlowerVendor::select('vendor_id', 'vendor_name', 'flower_ids')
            ->when(schema()->hasColumn('flower__vendor_details', 'status'), function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('vendor_name')
            ->get();

        // Master flower list
        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']);

        // Units
        $units = PoojaUnit::orderBy('unit_name')->get(['id', 'unit_name']);

        return view('admin.month-wise-flower-price', compact('vendors', 'flowers', 'units'));
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'vendor_id'   => 'required|string|exists:flower__vendor_details,vendor_id',
            'flower_ids'  => 'required|array|min:1',
            'flower_ids.*'=> 'integer',
            // Nested per-flower arrays will be validated in-loop for clearer errors
            'start_date'  => 'required|array',
            'end_date'    => 'required|array',
            'quantity'    => 'required|array',
            'unit_id'     => 'required|array',
            'price'       => 'required|array',
        ]);

        $vendorId   = $request->vendor_id;
        $flowerIds  = $request->flower_ids;

        // Pull vendor to ensure the selected flowers belong to this vendor (optional safety)
        $vendor = FlowerVendor::select('vendor_id', 'flower_ids')->findOrFail($vendorId);
        $allowed = collect($vendor->flower_ids ?? [])->map(fn($v)=>(int)$v)->all();

        DB::beginTransaction();

        try {
            foreach ($flowerIds as $fid) {
                $fid = (int)$fid;

                // OPTIONAL guard: skip flowers not assigned to vendor
                if (!in_array($fid, $allowed, true)) {
                    return back()->withInput()->with('error', 'One or more selected flowers are not assigned to this vendor.');
                }

                // Read per-flower inputs
                $start = $request->start_date[$fid] ?? null;
                $end   = $request->end_date[$fid] ?? null;
                $qty   = $request->quantity[$fid] ?? null;
                $unit  = $request->unit_id[$fid] ?? null;
                $price = $request->price[$fid] ?? null;

                // Per-flower validation
                if (!$start || !$end || !$qty || !$unit || $price === null) {
                    return back()->withInput()->with('error', 'Please fill all fields for each selected flower.');
                }

                // Logical date check
                $startDate = Carbon::parse($start);
                $endDate   = Carbon::parse($end);
                if ($endDate->lt($startDate)) {
                    return back()->withInput()->with('error', 'End date must be on or after start date for each flower.');
                }

                // Numeric checks
                if (!is_numeric($qty) || $qty <= 0) {
                    return back()->withInput()->with('error', 'Quantity must be a positive number.');
                }
                if (!is_numeric($price) || $price < 0) {
                    return back()->withInput()->with('error', 'Price must be zero or positive.');
                }

                // Save one record per flower
                MonthWiseFlowerPrice::create([
                    'vendor_id'       => $vendorId,
                    'product_id'      => $fid,                  // flower id
                    'start_date'      => $startDate->toDateString(),
                    'end_date'        => $endDate->toDateString(),
                    'quantity'        => $qty,
                    'unit_id'         => $unit,
                    'price_per_unit'  => $price,
                    // add other fields if your table includes them
                ]);
            }

            DB::commit();
            return redirect()->route('admin.flowerRegistration')
                ->with('success', 'Flower registration saved successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to save registration. '.$e->getMessage());
        }
    }

}
