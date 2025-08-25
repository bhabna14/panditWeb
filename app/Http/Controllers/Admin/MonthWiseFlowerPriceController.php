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
    // Load vendors that actually have price rows
    $vendors = FlowerVendor::with([
        'monthPrices' => function ($q) {
            $q->with(['product:product_id,name', 'unit:id,unit_name'])
              ->orderByDesc('id');
        },
    ])
    ->whereHas('monthPrices')
    ->orderBy('vendor_name')
    ->get();
        return view('admin.manage-month-wise-flower-price', compact('vendors'));

    }

    public function updateFlowerPrice(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'quantity'       => ['required', 'numeric', 'min:0'],
            'unit_id'        => ['required', 'string', 'max:50'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
        ]);

        $price = MonthWiseFlowerPrice::findOrFail($id);
        $price->update($validated); // requires $fillable as above

        return back()->with('success', 'Flower price updated successfully!');
    }

    public function deleteFlowerPrice($id)
    {
        try {
            $price = MonthWiseFlowerPrice::findOrFail($id);
            $price->delete();

            return response()->json(['message' => 'Flower price deleted successfully!'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Example: FK constraint violation
            return response()->json([
                'message' => 'Cannot delete this record because it is referenced elsewhere.'
            ], 409);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unexpected error. Please try again.'
            ], 500);
        }
    }

}
