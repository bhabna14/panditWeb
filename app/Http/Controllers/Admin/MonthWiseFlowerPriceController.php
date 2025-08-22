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
        $hasStatus = Schema::hasColumn('flower__vendor_details', 'status');

        $vendors = FlowerVendor::select('vendor_id', 'vendor_name', 'flower_ids')
            ->when($hasStatus, fn ($q) => $q->where('status', 'active'))
            ->orderBy('vendor_name')
            ->get();

        // Flowers are fetched per vendor via AJAX
        $units = PoojaUnit::orderBy('unit_name')->get(['id', 'unit_name']);

        return view('admin.month-wise-flower-price', compact('vendors', 'units'));
    }

    public function vendorFlowers(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required'
        ]);

        $vendor = FlowerVendor::select('vendor_id','flower_ids')->find($request->vendor_id);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }

        $raw = collect($vendor->flower_ids ?? [])
            ->map(fn($v) => strtoupper((string)$v))
            ->filter()
            ->unique()
            ->values();

        if ($raw->isEmpty()) {
            return response()->json([
                'success'   => true,
                'vendor_id' => $vendor->vendor_id,
                'flowers'   => [],
            ]);
        }

        // Split into full FLOW codes and digit-only ids
        $fullCodes = $raw->filter(fn($v) => preg_match('/^FLOW[0-9]+$/', $v))->values();
        $digitIds  = $raw->map(function ($v) {
                $d = preg_replace('/\D+/', '', $v);
                return $d !== '' ? (int)$d : null;
            })
            ->filter()
            ->unique()
            ->values();

        $query = FlowerProduct::query();

        if ($digitIds->isNotEmpty()) {
            $query->orWhereIn('product_id', $digitIds->all());
        }
        if ($fullCodes->isNotEmpty()) {
            $query->orWhereIn('product_id', $fullCodes->all());
            if (Schema::hasColumn('flower_products', 'product_code')) {
                $query->orWhereIn('product_code', $fullCodes->all());
            }
        }

        $flowers = $query->orderBy('name')->get(['product_id','name','odia_name']);

        return response()->json([
            'success'   => true,
            'vendor_id' => $vendor->vendor_id,
            'flowers'   => $flowers,
        ]);
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

}
