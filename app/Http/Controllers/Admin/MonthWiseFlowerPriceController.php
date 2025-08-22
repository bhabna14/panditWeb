<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // ✅ import Schema facade
use Carbon\Carbon;

use App\Models\FlowerVendor;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use App\Models\MonthWiseFlowerPrice;

class MonthWiseFlowerPriceController extends Controller
{

    public function create()
    {
        // Check once; avoid calling in closure
        $hasStatus = Schema::hasColumn('flower__vendor_details', 'status');

        // Vendors (with flower_ids)
        $vendors = FlowerVendor::select('vendor_id', 'vendor_name', 'flower_ids')
            ->when($hasStatus, fn ($q) => $q->where('status', 'active'))
            ->orderBy('vendor_name')
            ->get();

        // Master flowers (NO product_code here)
        $flowers = FlowerProduct::where(function ($q) {
                $q->where('category', 'Flower')->orWhere('category', 'flower');
            })
            ->orderBy('name')
            ->get(['product_id', 'name', 'odia_name']); // <-- removed 'product_code'

        // Units
        $units = PoojaUnit::orderBy('unit_name')->get(['id', 'unit_name']);

        return view('admin.month-wise-flower-price', compact('vendors', 'flowers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'    => 'required|string|exists:flower__vendor_details,vendor_id',
            'flower_ids'   => 'required|array|min:1',
            'flower_ids.*' => 'integer',

            // nested, validated manually per-flower
            'start_date'   => 'required|array',
            'end_date'     => 'required|array',
            'quantity'     => 'required|array',
            'unit_id'      => 'required|array',
            'price'        => 'required|array',
        ]);

        $vendorId  = $request->vendor_id;
        $flowerIds = $request->flower_ids;

        // Optional: ensure selected flowers belong to vendor
        $vendor   = FlowerVendor::select('vendor_id', 'flower_ids')->findOrFail($vendorId);
        $allowed  = collect($vendor->flower_ids ?? [])->map(fn($v) => (int)$v)->all();

        DB::beginTransaction();
        try {
            foreach ($flowerIds as $fid) {
                $fid = (int) $fid;

                if (!in_array($fid, $allowed, true)) {
                    return back()->withInput()->with('error', 'One or more selected flowers are not assigned to this vendor.');
                }

                $start = $request->start_date[$fid] ?? null;
                $end   = $request->end_date[$fid] ?? null;
                $qty   = $request->quantity[$fid] ?? null;
                $unit  = $request->unit_id[$fid] ?? null;
                $price = $request->price[$fid] ?? null;

                if (!$start || !$end || !$qty || !$unit || $price === null) {
                    return back()->withInput()->with('error', 'Please fill all fields for each selected flower.');
                }

                $startDate = Carbon::parse($start);
                $endDate   = Carbon::parse($end);
                if ($endDate->lt($startDate)) {
                    return back()->withInput()->with('error', 'End date must be on or after start date for each flower.');
                }

                if (!is_numeric($qty) || $qty <= 0) {
                    return back()->withInput()->with('error', 'Quantity must be a positive number.');
                }
                if (!is_numeric($price) || $price < 0) {
                    return back()->withInput()->with('error', 'Price must be zero or positive.');
                }

                MonthWiseFlowerPrice::create([
                    'vendor_id'      => $vendorId,
                    'product_id'     => $fid,
                    'start_date'     => $startDate->toDateString(),
                    'end_date'       => $endDate->toDateString(),
                    'quantity'       => $qty,
                    'unit_id'        => $unit,
                    'price_per_unit' => $price,
                ]);
            }

            DB::commit();
            // ✅ redirect back to this page (rename if you use a different route name)
            return redirect()->route('admin.monthWiseFlowerPrice')
                ->with('success', 'Month-wise flower prices saved successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save. '.$e->getMessage());
        }
    }

      public function vendorFlowers(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required'
        ]);

        /** @var FlowerVendor|null $vendor */
        $vendor = FlowerVendor::select('vendor_id','flower_ids')->find($request->vendor_id);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }

        // Resolve vendor.flower_ids (e.g., "FLOW3419542") -> numeric product_id
        $ids = collect($vendor->flower_ids ?? [])
            ->map(function ($val) {
                // Keep only digits (handles "FLOW12345" and "12345")
                $digits = preg_replace('/\D+/', '', (string)$val);
                return $digits !== '' ? (int)$digits : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json(['success' => true, 'vendor_id' => $vendor->vendor_id, 'flowers' => []]);
        }

        $flowers = FlowerProduct::whereIn('product_id', $ids)
            ->orderBy('name')
            ->get(['product_id','name','odia_name']);

        return response()->json([
            'success'   => true,
            'vendor_id' => $vendor->vendor_id,
            'flowers'   => $flowers,
        ]);
    }

}
