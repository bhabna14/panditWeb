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
    // public function create()
    // {
    //     $hasStatus = Schema::hasColumn('flower__vendor_details', 'status');

    //     $vendors = FlowerVendor::select('vendor_id', 'vendor_name', 'flower_ids')
    //         ->when($hasStatus, fn ($q) => $q->where('status', 'active'))
    //         ->orderBy('vendor_name')
    //         ->get();

    //     // Flowers are fetched per vendor via AJAX
    //     $units = PoojaUnit::orderBy('unit_name')->get(['id', 'unit_name']);

    //     return view('admin.month-wise-flower-price', compact('vendors', 'units'));
    // }

     public function create()
    {
        $vendors = FlowerVendor::all();
           $poojaUnits = PoojaUnit::all(['id','unit_name']); // load units
        return view('admin.month-wise-flower-price', compact('vendors','poojaUnits'));
    }

     public function vendorFlowers(Request $request)
    {
        $vendor = FlowerVendor::findOrFail($request->vendor_id);
        $flowerIds = $vendor->flower_ids ?? [];
        
        $flowers = FlowerProduct::whereIn('product_id', $flowerIds)->get();
        return response()->json($flowers);
    }

    // public function vendorFlowers(Request $request)
    // {
    //     $request->validate([
    //         'vendor_id' => 'required'
    //     ]);

    //     $vendor = FlowerVendor::select('vendor_id','flower_ids')->find($request->vendor_id);
    //     if (!$vendor) {
    //         return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
    //     }

    //     $raw = collect($vendor->flower_ids ?? [])
    //         ->map(fn($v) => strtoupper((string)$v))
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     if ($raw->isEmpty()) {
    //         return response()->json([
    //             'success'   => true,
    //             'vendor_id' => $vendor->vendor_id,
    //             'flowers'   => [],
    //         ]);
    //     }

    //     // Split into full FLOW codes and digit-only ids
    //     $fullCodes = $raw->filter(fn($v) => preg_match('/^FLOW[0-9]+$/', $v))->values();
    //     $digitIds  = $raw->map(function ($v) {
    //             $d = preg_replace('/\D+/', '', $v);
    //             return $d !== '' ? (int)$d : null;
    //         })
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     $query = FlowerProduct::query();

    //     if ($digitIds->isNotEmpty()) {
    //         $query->orWhereIn('product_id', $digitIds->all());
    //     }
    //     if ($fullCodes->isNotEmpty()) {
    //         $query->orWhereIn('product_id', $fullCodes->all());
    //         if (Schema::hasColumn('flower_products', 'product_code')) {
    //             $query->orWhereIn('product_code', $fullCodes->all());
    //         }
    //     }

    //     $flowers = $query->orderBy('name')->get(['product_id','name','odia_name']);

    //     return response()->json([
    //         'success'   => true,
    //         'vendor_id' => $vendor->vendor_id,
    //         'flowers'   => $flowers,
    //     ]);
    // }
 public function saveFlowerPrice(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required',
            'flower'    => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->flower as $flowerId => $entries) {
                foreach ($entries as $row) {
                    MonthWiseFlowerPrice::create([
                        'vendor_id'      => $request->vendor_id,
                        'product_id'     => $row['product_id'],
                        'start_date'     => $row['from_date'],
                        'end_date'       => $row['to_date'],
                        'quantity'       => $row['quantity'],
                        'unit_id'        => $row['unit'],
                        'price_per_unit' => $row['price'],
                    ]);
                }
            }
            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Flower prices saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
public function manageFlowerPrice()
{
    $transactions = MonthWiseFlowerPrice::with([
        'vendor:vendor_id,vendor_name',
        'product:product_id,name',
        'unit:id,unit_name'
    ])
    ->orderBy('id','desc')
    ->get();

    return view('admin.manage-month-wise-flower-price', compact('transactions'));
}

public function updateFlowerPrice(Request $request, $id)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'quantity'   => 'required|integer',
        'unit_id'    => 'required|exists:pooja_units,id',
        'price_per_unit' => 'required|numeric|min:0',
    ]);

    $price = MonthWiseFlowerPrice::findOrFail($id);
    $price->update($request->all());

    return redirect()->back()->with('success', 'Flower price updated successfully!');
}

public function deleteFlowerPrice($id)
{
    $price = MonthWiseFlowerPrice::findOrFail($id);
    $price->delete();

    return response()->json(['message' => 'Flower price deleted successfully!']);
}

}
