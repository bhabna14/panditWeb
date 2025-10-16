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
use App\Models\FlowerDetails;   // ← NEW: per-unit price & unit source

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function addproduct()
    {
        $pooja_list    = Poojalist::where('status', 'active')->get();

        // We now pull items from FlowerDetails (price is per unit in DB)
        $flowerDetails = FlowerDetails::orderBy('name')
            ->get(['id', 'name', 'unit', 'price']); // price = per-unit price

        return view('admin.add-product', compact('pooja_list', 'flowerDetails'));
    }
        
   public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required','string','max:255'],
            'odia_name'   => ['nullable','string','max:255'],
            'price'       => ['required','numeric','min:0','lte:mrp'],
            'mrp'         => ['required','numeric','min:0'],
            'discount'    => ['nullable','numeric','min:0'],
            'description' => ['required','string'],
            'category'    => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'pooja_id'    => ['nullable','integer'],

            'product_image' => ['required','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],

            // Package rows (FlowerDetails-backed)
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['nullable','integer','exists:flower__details,id'],
            'quantity'      => ['nullable','array'],
            'quantity.*'    => ['nullable','numeric','min:0'],
            'item_price'    => ['nullable','array'],
            'item_price.*'  => ['nullable','numeric','min:0'],

            // Subscription (single price + items)
            'duration'        => ['nullable','required_if:category,Subscription','in:1,3,6'],
            'per_day_price'   => ['nullable','required_if:category,Subscription','numeric','min:0'],

            // Subscription rows (FlowerDetails-backed)
            'sub_item_id'      => ['nullable','array'],
            'sub_item_id.*'    => ['nullable','integer','exists:flower__details,id'],
            'sub_quantity'     => ['nullable','array'],
            'sub_quantity.*'   => ['nullable','numeric','min:0'],
            'sub_item_price'   => ['nullable','array'],
            'sub_item_price.*' => ['nullable','numeric','min:0'],

            // Benefits
            'benefit'   => ['nullable','array'],
            'benefit.*' => ['nullable','string','max:255'],

            // Flower-only
            'mala_provided'    => ['nullable','required_if:category,Flower','in:yes,no'],
            'flower_available' => ['nullable','required_if:category,Flower','in:yes,no'],
            'available_from'   => ['nullable','required_if:flower_available,yes','date'],
            'available_to'     => ['nullable','required_if:flower_available,yes','date','after_or_equal:available_from'],

            // Stock
            'stock'            => ['nullable','integer','min:0'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // ─────────────────────────────────────────────────────────────────────
        // Build PACKAGE rows (store with flower_id from FlowerDetails)
        // ─────────────────────────────────────────────────────────────────────
        $packageRows = [];
        if (($validated['category'] ?? null) === 'Package') {
            $items  = (array) $request->input('item_id', []);
            $qtys   = (array) $request->input('quantity', []);

            foreach ($items as $i => $flowerDetailsId) {
                if ($flowerDetailsId === null || $flowerDetailsId === '') continue;

                $qty = $qtys[$i] ?? null;
                if ($qty === null || $qty === '' || !is_numeric($qty) || $qty < 0) {
                    return back()->withErrors(['package' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }

                /** @var FlowerDetails|null $fd */
                $fd = FlowerDetails::find($flowerDetailsId);
                if (!$fd) {
                    return back()->withErrors(['item_id' => 'Selected item not found. (Row '.($i+1).')'])->withInput();
                }

                // Use FlowerDetails pricing & unit
                $perUnit   = (float) $fd->price;     // price per FD unit
                $unitName  = (string) $fd->unit;
                $itemName  = (string) $fd->name;
                $flowerId  = (int) $fd->flower_id;   // << store THIS in package table

                $computed = $perUnit * (float) $qty;

                $packageRows[] = [
                    'flower_id' => $flowerId,            // << required
                    'item_id'   => (int) $flowerDetailsId,
                    'item_name' => $itemName,
                    'quantity'  => (float) $qty,
                    'unit_name' => $unitName,
                    'price'     => round($computed, 2),
                    'idx'       => $i,
                ];
            }

            if (count($packageRows) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // Build SUBSCRIPTION rows (store with flower_id from FlowerDetails)
        // ─────────────────────────────────────────────────────────────────────
        $subscriptionRows = [];
        if (($validated['category'] ?? null) === 'Subscription') {
            $sItems = (array) $request->input('sub_item_id', []);
            $sQtys  = (array) $request->input('sub_quantity', []);

            foreach ($sItems as $i => $flowerDetailsId) {
                if ($flowerDetailsId === null || $flowerDetailsId === '') continue;

                $qty = $sQtys[$i] ?? null;
                if ($qty === null || $qty === '' || !is_numeric($qty) || $qty < 0) {
                    return back()->withErrors(['subscription_items' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }

                /** @var FlowerDetails|null $fd */
                $fd = FlowerDetails::find($flowerDetailsId);
                if (!$fd) {
                    return back()->withErrors(['sub_item_id' => 'Selected subscription item not found. (Row '.($i+1).')'])->withInput();
                }

                $perUnit   = (float) $fd->price;
                $unitName  = (string) $fd->unit;
                $itemName  = (string) $fd->name;
                $flowerId  = (int) $fd->flower_id;   // << store THIS

                $computed = $perUnit * (float) $qty;

                $subscriptionRows[] = [
                    'flower_id' => $flowerId,            // << required
                    'item_id'   => (int) $flowerDetailsId,
                    'item_name' => $itemName,
                    'quantity'  => (float) $qty,
                    'unit_name' => $unitName,
                    'price'     => round($computed, 2),
                    'idx'       => $i,
                ];
            }

            if (count($subscriptionRows) < 1) {
                return back()->withErrors(['sub_item_id' => 'Please add at least one subscription item.'])->withInput();
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // Image upload
        // ─────────────────────────────────────────────────────────────────────
        $imageUrl = null;
        if ($request->hasFile('product_image')) {
            $hashName = $request->file('product_image')->hashName();
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $imageUrl = asset('product_images/' . $hashName);
        }

        // ─────────────────────────────────────────────────────────────────────
        // Derivations / flags
        // ─────────────────────────────────────────────────────────────────────
        $isFlower = (($validated['category'] ?? null) === 'Flower');
        $isSub    = (($validated['category'] ?? null) === 'Subscription');

        $productId = $isFlower ? 'FLOW' . mt_rand(1000000, 9999999) : 'PRODUCT' . mt_rand(1000000, 9999999);
        $slug      = Str::slug($validated['name'], '-');

        $benefitString = null;
        if (!empty($validated['benefit'])) {
            $cleanedBenefits = array_filter(array_map('trim', $validated['benefit']));
            $benefitString = $cleanedBenefits ? implode('#', $cleanedBenefits) : null;
        }

        $malaProvidedBool      = null;
        $isFlowerAvailableBool = null;
        if ($isFlower) {
            $malaProvidedBool      = isset($validated['mala_provided'])    ? $validated['mala_provided'] === 'yes'    : null;
            $isFlowerAvailableBool = isset($validated['flower_available']) ? $validated['flower_available'] === 'yes' : null;
        }

        // ─────────────────────────────────────────────────────────────────────
        // Persist (transaction)
        // ─────────────────────────────────────────────────────────────────────
        DB::transaction(function () use (
            $request, $validated, $productId, $slug, $benefitString, $imageUrl,
            $malaProvidedBool, $isFlowerAvailableBool, $isFlower, $isSub,
            $packageRows, $subscriptionRows
        ) {
            // Product
            $product = new FlowerProduct();
            $product->product_id    = $productId;
            $product->name          = $validated['name'];
            $product->odia_name     = $validated['odia_name'] ?? null;
            $product->slug          = $slug;
            $product->price         = (float) $validated['price'];
            $product->mrp           = (float) $validated['mrp'];
            $product->discount      = $validated['discount'] ?? null;
            $product->description   = $validated['description'];
            $product->category      = $validated['category'];

            $product->pooja_id      = ($validated['category'] === 'Package') ? $request->input('pooja_id', null) : null;

            $product->stock         = $isFlower ? null : ($request->filled('stock') ? (int)$request->input('stock') : 0);
            $product->duration      = $isSub ? $request->input('duration', null) : null;
            $product->per_day_price = $isSub ? (float) $request->input('per_day_price', 0) : null;

            $product->benefits      = $benefitString;
            $product->product_image = $imageUrl;

            // Flower-only
            $product->mala_provided       = $malaProvidedBool;
            $product->is_flower_available = $isFlowerAvailableBool;
            $product->available_from      = $isFlower ? $request->input('available_from') : null;
            $product->available_to        = $isFlower ? $request->input('available_to')   : null;

            $product->save();

            // Package items
            if (($validated['category'] ?? null) === 'Package' && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'flower_id'  => $row['flower_id'],  // << store the flower id
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],       // server-computed
                    ]);
                }
            }

            // Subscription items
            if ($isSub && !empty($subscriptionRows)) {
                foreach ($subscriptionRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'flower_id'  => $row['flower_id'],  // << store the flower id
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],       // server-computed
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $product = FlowerProduct::with([
            'pooja:id,pooja_name',
            'packageItems:id,product_id,item_name,quantity,unit,price',
        ])->findOrFail($id);

        // Still need Pooja list if category is Package
        $pooja_list = Poojalist::where('status', 'active')->get(['id','pooja_name']);

        // NEW: source items from FlowerDetails (has name, unit, per-unit price)
        $flowerDetails = FlowerDetails::orderBy('name')
            ->get(['id','name','unit','price']);

        // Blade updated below: resources/views/admin/edit-product.blade.php
        return view('admin.edit-product', compact('product', 'pooja_list', 'flowerDetails'));
    }

    public function deleteProduct($id)
    {
        $product = FlowerProduct::findOrFail($id);
        $product->update(['status' => 'deleted']);

        return redirect()->route('manageproduct')->with('success', 'Product deleted successfully.');
    }

    public function manageproduct()
    {
        $products = FlowerProduct::query()
            ->where('status','!=', 'deleted')
            ->select([
                'id','product_id','name','product_image','mrp','price','discount','stock',
                'category','status','benefits',
                'mala_provided','is_flower_available','available_from','available_to',
                'duration','per_day_price','pooja_id'
            ])
            ->with([
                'pooja:id,pooja_name',
                'packageItems:id,product_id,item_name,quantity,unit,price',
            ])
            ->orderByDesc('id')
            ->get();

        // Map FlowerDetails by name for quick lookup in the blade
        $fdByName = FlowerDetails::select('id','name','unit','price')->get()->keyBy('name');

        return view('admin.manage-product', compact('products','fdByName'));
    }

    public function toggleProduct($id)
    {
        $product = FlowerProduct::findOrFail($id);
        $product->status = ($product->status === 'active') ? 'deactive' : 'active';
        $product->save();

        return redirect()->back()->with('success', 'Product status updated successfully.');
    }
        
    public function updateProduct(Request $request, $id)
    {
        $product = FlowerProduct::with('packageItems')->findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required','string','max:255'],
            'odia_name'   => ['nullable','string','max:255'],
            'price'       => ['required','numeric','min:0','lte:mrp'],
            'mrp'         => ['required','numeric','min:0'],
            'discount'    => ['nullable','numeric','min:0'],
            'description' => ['required','string'],
            'category'    => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'pooja_id'    => ['nullable','integer'],

            'product_image' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],

            // Package rows (FlowerDetails-backed)
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['nullable','integer','exists:flower__details,id'],
            'quantity'      => ['nullable','array'],
            'quantity.*'    => ['nullable','numeric','min:0'],
            'item_price'    => ['nullable','array'],        // UX only, server will recompute
            'item_price.*'  => ['nullable','numeric','min:0'],

            // Subscription (single price + items)
            'duration'        => ['nullable','required_if:category,Subscription','in:1,3,6'],
            'per_day_price'   => ['nullable','required_if:category,Subscription','numeric','min:0'],

            // Subscription rows (FlowerDetails-backed)
            'sub_item_id'      => ['nullable','array'],
            'sub_item_id.*'    => ['nullable','integer','exists:flower__details,id'],
            'sub_quantity'     => ['nullable','array'],
            'sub_quantity.*'   => ['nullable','numeric','min:0'],
            'sub_item_price'   => ['nullable','array'],
            'sub_item_price.*' => ['nullable','numeric','min:0'],

            // Benefits
            'benefit'   => ['nullable','array'],
            'benefit.*' => ['nullable','string','max:255'],

            // Flower-only
            'mala_provided'    => ['nullable','required_if:category,Flower','in:yes,no'],
            'flower_available' => ['nullable','required_if:category,Flower','in:yes,no'],
            'available_from'   => ['nullable','required_if:flower_available,yes','date'],
            'available_to'     => ['nullable','required_if:flower_available,yes','date','after_or_equal:available_from'],

            // Stock
            'stock'            => ['nullable','integer','min:0'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // Build PACKAGE rows from FlowerDetails (server-side price = unit_price * qty)
        $packageRows = [];
        if (($validated['category'] ?? null) === 'Package') {
            $items  = (array) $request->input('item_id', []);
            $qtys   = (array) $request->input('quantity', []);

            foreach ($items as $i => $flowerDetailsId) {
                if ($flowerDetailsId === null || $flowerDetailsId === '') continue;

                $qty = $qtys[$i] ?? null;
                if ($qty === null || $qty === '' || !is_numeric($qty) || $qty < 0) {
                    return back()->withErrors(['package' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }

                $fd = FlowerDetails::find($flowerDetailsId);
                if (!$fd) {
                    return back()->withErrors(['item_id' => 'Selected item not found. (Row '.($i+1).')'])->withInput();
                }

                $perUnit  = (float) $fd->price;
                $unitName = (string) $fd->unit;
                $itemName = (string) $fd->name;

                $computed = $perUnit * (float) $qty;

                $packageRows[] = [
                    'item_id'   => (int) $flowerDetailsId,
                    'item_name' => $itemName,
                    'quantity'  => (float) $qty,
                    'unit_name' => $unitName,
                    'price'     => round($computed, 2),
                    'idx'       => $i,
                ];
            }

            if (count($packageRows) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }
        }

        // Build SUBSCRIPTION rows from FlowerDetails
        $subscriptionRows = [];
        if (($validated['category'] ?? null) === 'Subscription') {
            $sItems = (array) $request->input('sub_item_id', []);
            $sQtys  = (array) $request->input('sub_quantity', []);

            foreach ($sItems as $i => $flowerDetailsId) {
                if ($flowerDetailsId === null || $flowerDetailsId === '') continue;

                $qty = $sQtys[$i] ?? null;
                if ($qty === null || $qty === '' || !is_numeric($qty) || $qty < 0) {
                    return back()->withErrors(['subscription_items' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                }

                $fd = FlowerDetails::find($flowerDetailsId);
                if (!$fd) {
                    return back()->withErrors(['sub_item_id' => 'Selected subscription item not found. (Row '.($i+1).')'])->withInput();
                }

                $perUnit  = (float) $fd->price;
                $unitName = (string) $fd->unit;
                $itemName = (string) $fd->name;

                $computed = $perUnit * (float) $qty;

                $subscriptionRows[] = [
                    'item_id'   => (int) $flowerDetailsId,
                    'item_name' => $itemName,
                    'quantity'  => (float) $qty,
                    'unit_name' => $unitName,
                    'price'     => round($computed, 2),
                    'idx'       => $i,
                ];
            }

            if (count($subscriptionRows) < 1) {
                return back()->withErrors(['sub_item_id' => 'Please add at least one subscription item.'])->withInput();
            }
        }

        // Image upload (optional replace)
        $newImageUrl = null;
        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                $basename = basename(parse_url($product->product_image, PHP_URL_PATH) ?: '');
                $oldPath  = public_path('product_images/' . $basename);
                if ($basename && file_exists($oldPath)) { @unlink($oldPath); }
            }
            $hashName = $request->file('product_image')->hashName();
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $newImageUrl = asset('product_images/' . $hashName);
        }

        $isFlower = (($validated['category'] ?? null) === 'Flower');
        $isSub    = (($validated['category'] ?? null) === 'Subscription');

        $benefitString = null;
        if (!empty($validated['benefit'])) {
            $cleanedBenefits = array_filter(array_map('trim', $validated['benefit']));
            $benefitString = $cleanedBenefits ? implode('#', $cleanedBenefits) : null;
        }

        $malaProvidedBool      = null;
        $isFlowerAvailableBool = null;
        if ($isFlower) {
            $malaProvidedBool      = isset($validated['mala_provided'])    ? $validated['mala_provided'] === 'yes'    : null;
            $isFlowerAvailableBool = isset($validated['flower_available']) ? $validated['flower_available'] === 'yes' : null;
        }

        DB::transaction(function () use (
            $request, $validated, $product, $benefitString, $newImageUrl,
            $malaProvidedBool, $isFlowerAvailableBool, $isFlower, $isSub,
            $packageRows, $subscriptionRows
        ) {
            // Update core fields
            $product->name        = $validated['name'];
            $product->odia_name   = $validated['odia_name'] ?? null;
            $product->slug        = Str::slug($validated['name'], '-');
            $product->price       = (float) $validated['price'];
            $product->mrp         = (float) $validated['mrp'];
            $product->discount    = $validated['discount'] ?? null;
            $product->description = $validated['description'];
            $product->category    = $validated['category'];

            $product->pooja_id      = ($validated['category'] === 'Package') ? $request->input('pooja_id', null) : null;
            $product->stock         = $isFlower ? null : ($request->filled('stock') ? (int) $request->input('stock') : 0);
            $product->duration      = $isSub ? $request->input('duration', null) : null;
            $product->per_day_price = $isSub ? (float) $request->input('per_day_price', 0) : null;

            $product->benefits      = $benefitString;
            if ($newImageUrl) {
                $product->product_image = $newImageUrl;
            }

            // Flower-only
            $product->mala_provided       = $malaProvidedBool;
            $product->is_flower_available = $isFlowerAvailableBool;
            $product->available_from      = $isFlower ? $request->input('available_from') : null;
            $product->available_to        = $isFlower ? $request->input('available_to')   : null;

            $product->save();

            // Reset and re-create items
            PackageItem::where('product_id', $product->product_id)->delete();

            if (($validated['category'] ?? null) === 'Package' && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],   // server-computed
                    ]);
                }
            }

            if ($isSub && !empty($subscriptionRows)) {
                foreach ($subscriptionRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],   // server-computed
                    ]);
                }
            }
        });

        return redirect()->route('manageproduct')->with('success', 'Product updated successfully.');
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'item_name' => ['required','string','max:255','unique:poojaitem_list,item_name'],
        ]);

        // build unique slug
        $base = Str::slug($data['item_name'], '-');
        $slug = $base ?: 'item';
        $i = 2;
        
        while (Poojaitemlists::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        Poojaitemlists::create([
            'item_name'    => $data['item_name'],
            'slug'         => $slug,
        ]);

        return back()->with('success', 'Item added successfully.');
    }

    protected function uniqueSlug(string $base): string
    {
        $slug   = $base ?: 'item';
        $exists = Poojaitemlists::where('slug', $slug)->exists();

        if (!$exists) {
            return $slug;
        }

        $i = 2;
        while (Poojaitemlists::where('slug', "{$base}-{$i}")->exists()) {
            $i++;
        }

        return "{$base}-{$i}";
    }

}




