<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerCalendor;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class FlowerCalendarController extends Controller
{
    public function getFestivalCalendar()
    {

        $flowerNames = FlowerProduct::where('category', 'Flower')->where('status','active')->get();

        return view('admin.flower-festival-calendar',compact('flowerNames'));

    }

  public function saveFestivalCalendar(Request $request)
{
    try {
        // Validation
        $request->validate([
            'festival_name' => 'required|string|max:255',
            'festival_date' => 'required|date',
            'festival_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'package_price' => 'nullable|numeric',
            'related_flower' => 'nullable|array',
            'related_flower.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;

        // Image upload
        if ($request->hasFile('festival_image')) {
            $image = $request->file('festival_image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $storedPath = $image->storeAs('festival_images', $imageName, 'public');
            $imagePath = Storage::url($storedPath); // e.g. /storage/festival_images/xyz.jpg
        }

        // Handle related flowers
        $relatedFlowers = null;
        if (!empty($request->related_flower)) {
            $filtered = array_filter($request->related_flower); // remove empty
            $relatedFlowers = implode(',', $filtered);
        }

        // Save to DB
        FlowerCalendor::create([
            'festival_name'   => $request->festival_name,
            'festival_date'   => $request->festival_date,
            'festival_image'  => $imagePath,
            'related_flower'  => $relatedFlowers,
            'package_price'   => $request->package_price,
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
    return view('admin.manage-flower-festival-calendar', compact('festivals'));
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
public function updateFestivalCalendar(Request $request, $id)
{
    try {
        $festival = FlowerCalendor::findOrFail($id);

        $request->validate([
            'festival_name' => 'required|string|max:255',
            'festival_date' => 'required|date',
            'package_price' => 'nullable|numeric',
            'related_flower' => 'nullable|array',
            'related_flower.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'festival_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $festival->festival_image;

        if ($request->hasFile('festival_image')) {
            $image = $request->file('festival_image');
            $imageName = \Str::uuid() . '.' . $image->getClientOriginalExtension();
            $storedPath = $image->storeAs('festival_images', $imageName, 'public');
            $imagePath = \Storage::url($storedPath);
        }

        // Convert related flowers to comma-separated string
        $flowers = $request->related_flower ?? [];
        $relatedFlowerString = implode(',', array_filter($flowers));

        $festival->update([
            'festival_name'   => $request->festival_name,
            'festival_date'   => $request->festival_date,
            'festival_image'  => $imagePath,
            'package_price'   => $request->package_price,
            'related_flower'  => $relatedFlowerString,
            'description'     => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Festival updated successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}



}
