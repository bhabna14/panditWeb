<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferDetails;
use Illuminate\Support\Facades\Storage;

class OfferDetailsController extends Controller
{
    
    public function offerDetails()
    {
        return view('admin.offer.offer-details');
    }

 public function saveOfferDetails(Request $request)
    {
        try {
            // Validate inputs
            $request->validate([
                'main_header' => 'required|string|max:255',
                'sub_header'  => 'nullable|string|max:255',
                'content'     => 'nullable|string',
                'discount'    => 'nullable|numeric|min:0|max:100',
                'menu'        => 'nullable|array',
                'menu.*'      => 'nullable|string|max:255',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
            ]);

            // Process menu array to comma-separated string
            $menu = $request->menu ? implode(',', array_filter($request->menu)) : null;

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers', 'public');
                $imagePath = Storage::url($imagePath); // Get public URL
            }

            // Save to database
            OfferDetails::create([
                'main_header' => $request->main_header,
                'sub_header'  => $request->sub_header,
                'content'     => $request->content,
                'discount'    => $request->discount,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'menu'        => $menu,
                'image'       => $imagePath,
            ]);

            return redirect()->back()->with('success', 'Offer saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save offer: ' . $e->getMessage());
        }
    }

    public function manageOfferDetails()
    {
        $offers = OfferDetails::where('status','active')->get();

        return view('admin.offer.manage', compact('offers'));
    }

    public function updateOfferDetails(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:offer_details,id',
                'main_header' => 'required|string|max:255',
                'sub_header'  => 'nullable|string|max:255',
                'discount'    => 'nullable|numeric|min:0|max:100',
                'menu'        => 'nullable|string',
                'content'     => 'nullable|string',
            ]);

            $offer = OfferDetails::findOrFail($request->id);
            $offer->update([
                'main_header' => $request->main_header,
                'sub_header'  => $request->sub_header,
                'discount'    => $request->discount,
                'menu'        => $request->menu,
                'content'     => $request->content,
            ]);

            return redirect()->back()->with('success', 'Offer updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function deleteOfferDetails($id)
    {
        try {
            OfferDetails::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Offer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

}
