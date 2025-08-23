<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
use App\Models\Poojaitemlists;
use App\Models\PackageItem;
use App\Models\Poojalist;
use App\Models\PoojaUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

  public function addproduct()
{
    $pooja_list     = Poojalist::where('status', 'active')->get();
    $Poojaitemlist  = Poojaitemlists::where('status', 'active')->get(); // variants no longer needed
    $pooja_units    = PoojaUnit::orderBy('unit_name')->get();

    return view('admin.add-product', compact('Poojaitemlist', 'pooja_list', 'pooja_units'));
}

  public function createProduct(Request $request)
    {
        // 1) Validate base product + package arrays (no variants)
        $validated = $request->validate([
            'name'        => ['required','string','max:255'],
            'odia_name'   => ['nullable','string','max:255'],
            'price'       => ['required','numeric','min:0','lte:mrp'],
            'mrp'         => ['required','numeric','min:0'],
            'description' => ['required','string'],
            'category'    => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'pooja_id'    => ['nullable','integer'],

            'product_image' => ['required','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],

            // Package rows (no variants)
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['nullable','integer'],
            'quantity'      => ['nullable','array'],
            'quantity.*'    => ['nullable','numeric','min:0'],
            'unit_id'       => ['nullable','array'],
            'unit_id.*'     => ['nullable','integer'],
            'item_price'    => ['nullable','array'],
            'item_price.*'  => ['nullable','numeric','min:0'],

            // Benefits
            'benefit'   => ['nullable','array'],
            'benefit.*' => ['nullable','string','max:255'],

            // Flower-only
            'mala_provided'    => ['nullable','required_if:category,Flower','in:yes,no'],
            'flower_available' => ['nullable','required_if:category,Flower','in:yes,no'],
            // Dates are required only when flower_available == yes
            'available_from'   => ['nullable','required_if:flower_available,yes','date'],
            'available_to'     => ['nullable','required_if:flower_available,yes','date','after_or_equal:available_from'],

            // Subscription-only
            'duration'         => ['nullable','required_if:category,Subscription','in:1,3,6'],

            // Stock (hidden for Flower; still validate if present)
            'stock'            => ['nullable','integer','min:0'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // 1a) Build & validate package rows if category is Package
        $packageRows = [];
        if (($validated['category'] ?? null) === 'Package') {
            $items    = (array) $request->input('item_id', []);
            $qtys     = (array) $request->input('quantity', []);
            $unitIds  = (array) $request->input('unit_id', []);
            $prices   = (array) $request->input('item_price', []);

            // collect valid row indices based on item_id presence
            foreach ($items as $i => $itemId) {
                if ($itemId === null || $itemId === '' ) { continue; }

                $qty   = $qtys[$i]    ?? null;
                $unit  = $unitIds[$i] ?? null;
                $price = $prices[$i]  ?? null;

                if ($qty === null || $qty === '' || $unit === null || $unit === '' || $price === null || $price === '') {
                    return back()
                        ->withErrors(['package' => 'Each package row must include Item, Qty, Unit, and Item Price. (Row '.($i+1).')'])
                        ->withInput();
                }

                if (!is_numeric($qty) || $qty < 0) {
                    return back()->withErrors(['package' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }
                if (!is_numeric($price) || $price < 0) {
                    return back()->withErrors(['package' => 'Item Price must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }

                $packageRows[] = [
                    'item_id'  => (int)$itemId,
                    'quantity' => (float)$qty,
                    'unit_id'  => (int)$unit,
                    'price'    => (float)$price,
                    'idx'      => $i, // keep for error messages if needed
                ];
            }

            if (count($packageRows) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }

            // Resolve item names & unit names up-front (save names, not IDs)
            $itemIds  = collect($packageRows)->pluck('item_id')->unique()->values();
            $unitIdsC = collect($packageRows)->pluck('unit_id')->unique()->values();

            $itemNamesById = Poojaitemlists::whereIn('id', $itemIds)->pluck('item_name', 'id');
            $unitNamesById = PoojaUnit::whereIn('id', $unitIdsC)->pluck('unit_name', 'id');

            // ensure everything exists
            if ($itemNamesById->count() !== $itemIds->count()) {
                return back()->withErrors(['item_id' => 'One or more selected items were not found.'])->withInput();
            }
            if ($unitNamesById->count() !== $unitIdsC->count()) {
                return back()->withErrors(['unit_id' => 'One or more selected units were not found.'])->withInput();
            }

            // attach resolved names for insert
            $packageRows = array_map(function ($row) use ($itemNamesById, $unitNamesById) {
                $row['item_name'] = (string) ($itemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($unitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $packageRows);
        }

        // 2) Handle image
        $imageUrl = null;
        if ($request->hasFile('product_image')) {
            $hashName = $request->file('product_image')->hashName(); // xxxxx.jpg
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $imageUrl = asset('product_images/' . $hashName);
        }

        // 3) Derivations
        $isFlower  = (($validated['category'] ?? null) === 'Flower');
        $productId = $isFlower
            ? 'FLOW'    . mt_rand(1000000, 9999999)
            : 'PRODUCT' . mt_rand(1000000, 9999999);

        $slug = Str::slug($validated['name'], '-');

        $benefitString = null;
        if (!empty($validated['benefit'])) {
            $cleanedBenefits = array_filter(array_map('trim', $validated['benefit']));
            $benefitString = $cleanedBenefits ? implode('#', $cleanedBenefits) : null;
        }

        // radios -> booleans
        $malaProvidedBool      = null;
        $isFlowerAvailableBool = null;
        if ($isFlower) {
            $malaProvidedBool      = isset($validated['mala_provided'])    ? $validated['mala_provided'] === 'yes'    : null;
            $isFlowerAvailableBool = isset($validated['flower_available']) ? $validated['flower_available'] === 'yes' : null;
        }

        // 4) Persist
        DB::transaction(function () use (
            $request, $validated, $productId, $slug, $benefitString, $imageUrl,
            $malaProvidedBool, $isFlowerAvailableBool, $isFlower, $packageRows
        ) {
            $product = new FlowerProduct();
            $product->product_id    = $productId;
            $product->name          = $validated['name'];
            $product->odia_name     = $validated['odia_name'] ?? null;
            $product->slug          = $slug;
            $product->price         = $validated['price'];
            $product->mrp           = $validated['mrp'];
            $product->description   = $validated['description'];
            $product->category      = $validated['category'];
            $product->pooja_id      = $request->input('pooja_id', null);

            // For Flower, ignore stock & duration
            $product->stock         = $isFlower ? null : ($request->filled('stock') ? (int)$request->input('stock') : 0);
            $product->duration      = $isFlower ? null : $request->input('duration', null);

            $product->benefits      = $benefitString;
            $product->product_image = $imageUrl;

            // Flower-only
            $product->mala_provided       = $malaProvidedBool;       // bool|null
            $product->is_flower_available = $isFlowerAvailableBool;  // bool|null
            $product->available_from      = $isFlower ? $request->input('available_from') : null; // Y-m-d
            $product->available_to        = $isFlower ? $request->input('available_to')   : null;

            $product->save();

            // Package items (save item_name + unit name + qty + price)
            if (($validated['category'] ?? null) === 'Package' && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'], // save name, not id
                        'price'      => $row['price'],
                    ]);
                }
            }
        });

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
        $product = FlowerProduct::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'name'        => ['required','string','max:255'],
            'odia_name'   => ['nullable','string','max:255'],
            'mrp'         => ['required','numeric','min:0'],
            'price'       => ['required','numeric','min:0','lte:mrp'],
            'category'    => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'stock'       => ['nullable','integer','min:0'],
            'duration'    => ['nullable','required_if:category,Subscription','in:1,3,6'],
            'product_image' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],
            'description' => ['required','string'],

            // Package items
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['integer'],
            'variant_id'    => ['nullable','array'],
            'variant_id.*'  => ['nullable','integer'],

            // Benefits (edit form should submit as benefits[])
            'benefits'      => ['nullable','array'],
            'benefits.*'    => ['nullable','string','max:255'],

            // Flower-only
            'mala_provided'    => ['nullable','required_if:category,Flower','in:yes,no'],
            'flower_available' => ['nullable','required_if:category,Flower','in:yes,no'],
            'available_from'   => ['nullable','required_if:category,Flower','date'],
            'available_to'     => ['nullable','required_if:category,Flower','date','after_or_equal:available_from'],

            // Package-only (shown along with Package fields)
            'pooja_id'         => ['nullable','integer'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // Extra server-side checks for Package
        if (($validated['category'] ?? null) === 'Package') {
            $items    = $request->input('item_id', []);
            $variants = $request->input('variant_id', []);
            if (!is_array($items) || count($items) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }
            if (count($items) !== count($variants)) {
                return back()->withErrors(['variant_id' => 'Each package item must have a corresponding variant.'])->withInput();
            }
        }

        DB::transaction(function () use ($request, $validated, $product) {
            $isFlower       = ($validated['category'] === 'Flower');
            $isSubscription = ($validated['category'] === 'Subscription');
            $isPackage      = ($validated['category'] === 'Package');

            // Build benefits string (`#`-joined)
            $benefitString = null;
            if (!empty($validated['benefits'])) {
                $cleaned = array_filter(array_map('trim', $validated['benefits']));
                $benefitString = $cleaned ? implode('#', $cleaned) : null;
            }

            // Slug (regenerate if name changed)
            if ($product->name !== $validated['name']) {
                $product->slug = Str::slug($validated['name'], '-');
            }

            // Map yes/no → booleans for flower
            $malaProvidedBool      = null;
            $flowerAvailableBool   = null;
            if ($isFlower) {
                $malaProvidedBool    = $request->filled('mala_provided')    ? $request->mala_provided === 'yes'    : null;
                $flowerAvailableBool = $request->filled('flower_available') ? $request->flower_available === 'yes' : null;
            }

            // Assign standard fields
            $product->name        = $validated['name'];
            $product->odia_name   = $validated['odia_name'] ?? null;
            $product->price       = $validated['price'];
            $product->mrp         = $validated['mrp'];
            $product->description = $validated['description'];
            $product->category    = $validated['category'];

            // Stock & Duration handling
            $product->stock    = $isFlower ? null : ($request->input('stock') !== null ? (int)$request->input('stock') : ($product->stock ?? 0));
            $product->duration = $isSubscription ? $request->input('duration') : null;

            // Package-related: save pooja_id only for Package; clear otherwise
            $product->pooja_id = $isPackage ? $request->input('pooja_id', null) : null;

            // Benefits
            $product->benefits = $benefitString;

            // Flower-only fields
            $product->mala_provided       = $malaProvidedBool;       // bool|null
            $product->is_flower_available = $flowerAvailableBool;    // bool|null
            $product->available_from      = $isFlower ? $request->input('available_from') : null; // Y-m-d
            $product->available_to        = $isFlower ? $request->input('available_to')   : null;

            // Image upload (replace old)
            if ($request->hasFile('product_image')) {
                if ($product->product_image && file_exists(public_path('product_images/' . basename($product->product_image)))) {
                    @unlink(public_path('product_images/' . basename($product->product_image)));
                }
                $hashName = $request->file('product_image')->hashName();
                $request->file('product_image')->move(public_path('product_images'), $hashName);
                $product->product_image = asset('product_images/' . $hashName);
            }

            $product->save();

            // Package items: rebuild if Package; otherwise delete any existing
            \App\Models\PackageItem::where('product_id', $product->product_id)->delete();
            if ($isPackage && $request->filled('item_id')) {
                foreach ((array)$request->input('item_id') as $index => $itemId) {
                    if (!empty($itemId)) {
                        \App\Models\PackageItem::create([
                            'product_id' => $product->product_id,
                            'item_id'    => $itemId,
                            'variant_id' => $request->input("variant_id.$index"),
                        ]);
                    }
                }
            }
        });

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
