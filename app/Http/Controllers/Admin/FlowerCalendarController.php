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

}
