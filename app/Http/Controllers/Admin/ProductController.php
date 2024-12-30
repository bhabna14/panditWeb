<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use App\Models\Poojaitemlists;
use App\Models\PackageItem;
use App\Models\Poojalist;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    public function addproduct()
    {
        $pooja_list = Poojalist::where('status', 'active')->get();

        $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();
        return view('admin.add-product', compact('Poojaitemlist','pooja_list'));
    }


        public function createProduct(Request $request)
        {
            // Validate the incoming request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'mrp' => 'required|numeric',
                'description' => 'required|string',
                'category' => 'required|string',
                'pooja_id' => 'nullable|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',
                'item_id' => 'nullable|array',
                'variant_id' => 'nullable|array',
            ]);
    
            // Handle the product image upload
            $imageUrl = null;
            if ($request->hasFile('product_image')) {
                $imagePath = 'product_images/' . $request->file('product_image')->hashName();
                $request->file('product_image')->move(public_path('product_images'), $imagePath);
                $imageUrl = asset($imagePath);
            }
    
            // Generate a unique product ID based on the category
            $productId = $validated['category'] === 'Flower' 
                ? 'FLOW' . mt_rand(1000000, 9999999) 
                : 'PRODUCT' . mt_rand(1000000, 9999999);
    
            // Generate a slug for the product name
            $slug = Str::slug($validated['name'], '-');
    
            // Create the product record
            $product = new FlowerProduct();
            $product->product_id = $productId;
            $product->name = $validated['name'];
            $product->slug = $slug;
            $product->price = $validated['price'];
            $product->mrp = $validated['mrp'];
            $product->description = $validated['description'];
            $product->category = $validated['category'];
            $product->pooja_id = $request->input('pooja_id', null);
            $product->stock = $request->input('stock', 0);
            $product->duration = $request->input('duration', null);
            $product->product_image = $imageUrl;
            $product->save();
    
            // Handle package items if the category is "Package"
            if ($validated['category'] === 'Package' && $request->has('item_id')) {
                foreach ($validated['item_id'] as $key => $itemId) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_id' => $itemId,
                        'variant_id' => $request->variant_id[$key] ?? null,
                    ]);
                }
            }
    
            // Redirect back with a success message
            return redirect()->back()->with('success', 'Product created successfully.');
        }

    public function editProduct($id)
    {
        $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();
        $product = FlowerProduct::findOrFail($id);
        $selectedItems = PackageItem::where('product_id', $product->product_id)->get();

        return view('admin.edit-product', compact('product', 'Poojaitemlist', 'selectedItems'));
    }
    
    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'stock' => 'nullable|numeric',
            'duration' => 'nullable|numeric',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required|string',
            'item_id' => 'nullable|array',
            'variant_id' => 'nullable|array',
        ]);
    
        $product = FlowerProduct::findOrFail($id);
    
        $product->update($request->except('product_image'));
    
        if ($request->hasFile('product_image')) {
            // Delete the old image if it exists
            if ($product->product_image) {
                Storage::delete('public/' . $product->product_image);
            }
    
            // New image storage logic
            $imagePath = 'product_images/' . $request->file('product_image')->hashName();
            $request->file('product_image')->move(public_path('product_images'), $imagePath);
            $imageUrl = asset($imagePath);
    
            // Update the product's image URL
            $product->product_image = $imageUrl;
            $product->save();
        }
    
        // Update package items
        PackageItem::where('product_id', $product->product_id)->delete();
        if ($request->item_id && $request->variant_id) {
            foreach ($request->item_id as $index => $itemId) {
                if (!empty($itemId) && !empty($request->variant_id[$index])) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_id' => $itemId,
                        'variant_id' => $request->variant_id[$index],
                    ]);
                }
            }
        }
    
        return redirect()->route('admin.edit-product', $product->id)->with('success', 'Product updated successfully.');
    }
    
    
    public function deleteProduct($id)
    {
        $product = FlowerProduct::findOrFail($id);
        $product->update(['status' => 'deleted']);

        return redirect()->route('manageproduct')->with('success', 'Product deleted successfully.');
    }

    public function manageproduct()
    {
        $products = FlowerProduct::where('status', 'active')->with('packageItems.item', 'packageItems.variant')->get();
        return view('admin.manage-product', compact('products'));
    }
}
