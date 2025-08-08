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
            'name'           => 'required|string|max:255',
            'odia_name'      => 'nullable|string|max:255',
            'price'          => 'required|numeric',
            'mrp'            => 'required|numeric',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'pooja_id'       => 'nullable|string',
            'product_image'  => 'required|image|mimes:jpeg,png,jpg,gif|max:10000',

            // Package items
            'item_id'        => 'nullable|array',
            'variant_id'     => 'nullable|array',

            // Benefits
            'benefit'        => 'nullable|array',
            'benefit.*'      => 'nullable|string|max:255',

            // New Flower-only fields
            'mala_provided'    => 'nullable|required_if:category,Flower|in:yes,no',
            'flower_available' => 'nullable|required_if:category,Flower|in:yes,no', // yes=Active, no=Inactive
        ]);

        // Handle the product image upload (fixed path handling)
        $imageUrl = null;
        if ($request->hasFile('product_image')) {
            $hashName = $request->file('product_image')->hashName(); // e.g. xxxxx.jpg
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $imageUrl = asset('product_images/' . $hashName);
        }

        // Generate a unique product ID based on the category
        $productId = ($validated['category'] === 'Flower')
            ? 'FLOW' . mt_rand(1000000, 9999999)
            : 'PRODUCT' . mt_rand(1000000, 9999999);

        // Generate a slug for the product name
        $slug = Str::slug($validated['name'], '-');

        // Join benefits as '#' separated string (as in your code)
        $benefitString = null;
        if (!empty($validated['benefit'])) {
            $cleanedBenefits = array_filter(array_map('trim', $validated['benefit']));
            $benefitString = implode('#', $cleanedBenefits);
        }

        // Normalize new flower-only fields (map yes/no -> boolean)
        $malaProvidedBool = null;
        $isFlowerAvailableBool = null;

        if ($validated['category'] === 'Flower') {
            $malaProvidedBool       = isset($validated['mala_provided']) ? ($validated['mala_provided'] === 'yes') : null;
            $isFlowerAvailableBool  = isset($validated['flower_available']) ? ($validated['flower_available'] === 'yes') : null;
        }

        // Create the product record
        $product = new FlowerProduct();
        $product->product_id        = $productId;
        $product->name              = $validated['name'];
        $product->odia_name         = $validated['odia_name'] ?? null;
        $product->slug              = $slug;
        $product->price             = $validated['price'];
        $product->mrp               = $validated['mrp'];
        $product->description       = $validated['description'];
        $product->category          = $validated['category'];
        $product->pooja_id          = $request->input('pooja_id', null);
        $product->stock             = $request->input('stock', 0);
        $product->duration          = $request->input('duration', null);
        $product->benefits          = $benefitString; // '#' separated, same as before
        $product->product_image     = $imageUrl;

        // New columns
        $product->mala_provided     = $malaProvidedBool;       // boolean|null
        $product->is_flower_available = $isFlowerAvailableBool; // boolean|null

        $product->save();

        // Handle package items if the category is "Package"
        if ($validated['category'] === 'Package' && $request->has('item_id')) {
            foreach ($validated['item_id'] as $key => $itemId) {
                PackageItem::create([
                    'product_id' => $product->product_id,
                    'item_id'    => $itemId,
                    'variant_id' => $request->variant_id[$key] ?? null,
                ]);
            }
        }

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
        // Validate inputs (add new fields + flower-only rules)
        $request->validate([
            'name'              => 'required|string|max:255',
            'odia_name'         => 'nullable|string|max:255',
            'mrp'               => 'required|numeric',
            'price'             => 'required|numeric',
            'category'          => 'required|string',
            'stock'             => 'nullable|numeric',
            'duration'          => 'nullable|numeric',
            'product_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10000',
            'description'       => 'required|string',
            'item_id'           => 'nullable|array',
            'variant_id'        => 'nullable|array',

            // benefits[] -> join with '#'
            'benefits'          => 'nullable|array',
            'benefits.*'        => 'nullable|string|max:255',

            // New flower-only fields (expect "yes"/"no")
            'mala_provided'     => 'nullable|required_if:category,Flower|in:yes,no',
            'flower_available'  => 'nullable|required_if:category,Flower|in:yes,no',
        ]);

        $product = FlowerProduct::findOrFail($id);

        // Build benefits string
        $benefitString = null;
        if ($request->filled('benefits')) {
            $benefitArray  = array_filter(array_map('trim', $request->benefits));
            $benefitString = implode('#', $benefitArray);
        }

        // Update slug if name changed
        if ($product->name !== $request->name) {
            $product->slug = \Illuminate\Support\Str::slug($request->name, '-');
        }

        // Map yes/no -> boolean for flower-only fields; null for non-Flower
        $malaProvidedBool = null;
        $flowerAvailableBool = null;
        if ($request->category === 'Flower') {
            if ($request->filled('mala_provided')) {
                $malaProvidedBool = $request->mala_provided === 'yes';
            }
            if ($request->filled('flower_available')) {
                $flowerAvailableBool = $request->flower_available === 'yes';
            }
        }

        // Mass-assign safe fields (exclude file + arrays + derived)
        $product->fill($request->except([
            'product_image',
            'benefits',
            'item_id',
            'variant_id',
            'mala_provided',
            'flower_available',
        ]));

        // Set derived/new fields
        $product->benefits            = $benefitString;           // '#'-joined
        $product->odia_name           = $request->input('odia_name'); // nullable
        $product->mala_provided       = $malaProvidedBool;        // boolean|null
        $product->is_flower_available = $flowerAvailableBool;     // boolean|null

        // Handle image upload (fix: don't double-prefix the directory)
        if ($request->hasFile('product_image')) {
            // delete old file if present
            if ($product->product_image && file_exists(public_path('product_images/' . basename($product->product_image)))) {
                @unlink(public_path('product_images/' . basename($product->product_image)));
            }

            $hashName = $request->file('product_image')->hashName(); // e.g. abc123.jpg
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $product->product_image = asset('product_images/' . $hashName);
        }

        $product->save();

        // Rebuild package items only if category is Package
        \App\Models\PackageItem::where('product_id', $product->product_id)->delete();
        if ($request->category === 'Package' && $request->filled('item_id')) {
            foreach ($request->item_id as $index => $itemId) {
                if (!empty($itemId)) {
                    \App\Models\PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_id'    => $itemId,
                        'variant_id' => $request->variant_id[$index] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.edit-product', $product->id)
            ->with('success', 'Product updated successfully.');
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
