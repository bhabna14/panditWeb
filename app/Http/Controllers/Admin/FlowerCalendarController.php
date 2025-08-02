<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerCalendor;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FlowerCalendarController extends Controller
{
    public function getFestivalCalendar()
    {
        $packages = FlowerProduct::where('status', 'active')->where('category','package')->get();

        $flowerNames = FlowerProduct::where('category', 'Flower')->where('status','active')->get();

        return view('admin.flower-festival-calendar',compact('flowerNames', 'packages'));

    }

 public function saveFestivalCalendar(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'festival_name' => 'required|string|max:255',
            'festival_date' => 'required|date',
            'festival_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'package_price' => 'nullable|numeric',
            'related_flower' => 'nullable|array',
            'related_flower.*' => 'nullable|string|max:255',
            'product_id' => 'nullable|array',
            'product_id.*' => 'nullable|string|exists:flower_products,product_id',
            'description' => 'nullable|string',
        ]);

        // Handle image
        $imagePath = null;
        if ($request->hasFile('festival_image')) {
            $image = $request->file('festival_image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $storedPath = $image->storeAs('festival_images', $imageName, 'public');
            $imagePath = Storage::url($storedPath);
        }

        // Related flowers
        $relatedFlowers = $request->filled('related_flower')
            ? implode(',', array_filter($request->related_flower))
            : null;

        // Packages
        $productIds = $request->filled('product_id')
            ? implode(',', array_filter($request->product_id))
            : null;

        // Save to DB
        FlowerCalendor::create([
            'festival_name'   => $request->festival_name,
            'festival_date'   => $request->festival_date,
            'festival_image'  => $imagePath,
            'related_flower'  => $relatedFlowers,
            'package_price'   => $request->package_price,
            'product_id'      => $productIds,
            'description'     => $request->description,
        ]);

        return redirect()->back()->with('success', 'Festival calendar saved successfully.');
    } catch (ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput()
            ->with('validation_errors', true);
    } catch (\Throwable $e) {
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

public function manageFestivalCalendar()
{
    $festivals = FlowerCalendor::where('status','active')->get();

       foreach ($festivals as $festival) {
        $productIds = explode(',', $festival->product_id ?? '');
        $productNames = FlowerProduct::whereIn('product_id', $productIds)->pluck('name')->toArray();
        $offer->package_names = implode(', ', $productNames); // Attach for use in view
        }

        $packages = FlowerProduct::where('category', 'package')->where('status', 'active')->get();

    return view('admin.manage-flower-festival-calendar', compact('festivals', 'packages'));
}

public function deleteFestivalCalendar($id)
{
    try {
        $festival = FlowerCalendor::findOrFail($id);

        if ($festival->festival_image && Storage::disk('public')->exists(str_replace('/storage/', '', $festival->festival_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $festival->festival_image));
        }

        $festival->delete();

        return redirect()->route('admin.manageFestivalCalendar')->with('success', 'Festival deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->route('admin.manageFestivalCalendar')->with('error', 'Error deleting festival: ' . $e->getMessage());
    }
}


}
