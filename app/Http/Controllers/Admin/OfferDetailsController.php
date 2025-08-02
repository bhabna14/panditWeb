<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferDetails;
use App\Models\FlowerProduct;

use Illuminate\Support\Facades\Storage;

class OfferDetailsController extends Controller
{
    
    public function offerDetails()
    {
        $packages = FlowerProduct::where('status', 'active')->where('category','package')->get();

        return view('admin.offer.offer-details' , compact('packages'));
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
                'menu_items'  => 'nullable|array',
                'menu_items.*'=> 'nullable|string|max:255',
                'product_id'  => 'nullable|array',
                'product_id.*'=> 'nullable|string|exists:flower_products,product_id',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
            ]);

            // Process arrays to comma-separated strings
            $menu = $request->filled('menu_items') 
                ? implode(',', array_filter(array_map('trim', $request->menu_items))) 
                : null;

            $packages = $request->filled('product_id') 
                ? implode(',', array_filter($request->product_id)) 
                : null;

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $uploaded = $request->file('image')->store('offers', 'public');
                $imagePath = Storage::url($uploaded); // public path like /storage/offers/xxx.jpg
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
                'product_id'  => $packages,
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

        foreach ($offers as $offer) {
        $productIds = explode(',', $offer->product_id ?? '');
        $productNames = FlowerProduct::whereIn('product_id', $productIds)->pluck('name')->toArray();
        $offer->package_names = implode(', ', $productNames); // Attach for use in view
        }

        $packages = FlowerProduct::where('category', 'package')->where('status', 'active')->get();


        return view('admin.offer.manage-offer-details', compact('offers', 'packages'));
    }
    
    public function updateOfferDetails(Request $request)
    {
        try {
            // Validate the request fields based on modal input names
            $request->validate([
                'id' => 'required|exists:offer_details,id',
                'main_header' => 'required|string|max:255',
                'sub_header'  => 'nullable|string|max:255',
                'discount'    => 'nullable|numeric|min:0|max:100',
                'menu'        => 'nullable|array',
                'menu.*'      => 'nullable|string|max:255',
                'product_id'  => 'nullable|array',
                'product_id.*'=> 'nullable|string|exists:flower_products,product_id',
                'content'     => 'nullable|string',
                'start_date'  => 'nullable|date',
                'end_date'    => 'nullable|date|after_or_equal:start_date',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Fetch the offer
            $offer = OfferDetails::findOrFail($request->id);

            // Convert menu[] and product_id[] arrays to comma-separated strings
            $menu = $request->has('menu')
                ? implode(',', array_filter(array_map('trim', $request->menu)))
                : null;

            $productIds = $request->has('product_id')
                ? implode(',', array_filter($request->product_id))
                : null;

            // Handle image upload if a new file is provided
            $imagePath = $offer->image;
            if ($request->hasFile('image')) {
                // Delete existing image if present
                if ($imagePath && \Storage::disk('public')->exists(str_replace('/storage/', '', $imagePath))) {
                    \Storage::disk('public')->delete(str_replace('/storage/', '', $imagePath));
                }

                // Store new image
                $storedImage = $request->file('image')->store('offers', 'public');
                $imagePath = \Storage::url($storedImage);
            }

            // Update all fields
            $offer->update([
                'main_header' => $request->main_header,
                'sub_header'  => $request->sub_header,
                'discount'    => $request->discount,
                'menu'        => $menu,
                'product_id'  => $productIds,
                'content'     => $request->content,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'image'       => $imagePath,
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
