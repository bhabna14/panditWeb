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
        // 1) Validate base product + arrays
        $validated = $request->validate([
            'name'        => ['required','string','max:255'],
            'odia_name'   => ['nullable','string','max:255'],
            'price'       => ['required','numeric','min:0','lte:mrp'],
            'mrp'         => ['required','numeric','min:0'],
            'discount'    => ['nullable','numeric','min:0'],
            'description' => ['required','string'],
            'category'    => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'pooja_id'    => ['nullable','integer'], // nulled unless category=Package

            'product_image' => ['required','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],

            // Package rows
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['nullable','integer'],
            'quantity'      => ['nullable','array'],
            'quantity.*'    => ['nullable','numeric','min:0'],
            'unit_id'       => ['nullable','array'],
            'unit_id.*'     => ['nullable','integer'],
            'item_price'    => ['nullable','array'],
            'item_price.*'  => ['nullable','numeric','min:0'],

            // Subscription (single price + items)
            'duration'        => ['nullable','required_if:category,Subscription','in:1,3,6'],
            'per_day_price'   => ['nullable','required_if:category,Subscription','numeric','min:0'],

            // Subscription item rows
            'sub_item_id'      => ['nullable','array'],
            'sub_item_id.*'    => ['nullable','integer'],
            'sub_quantity'     => ['nullable','array'],
            'sub_quantity.*'   => ['nullable','numeric','min:0'],
            'sub_unit_id'      => ['nullable','array'],
            'sub_unit_id.*'    => ['nullable','integer'],
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

            // Stock (hidden for Flower; still validate if present)
            'stock'            => ['nullable','integer','min:0'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // 1a) Build & validate PACKAGE rows
        $packageRows = [];
        if (($validated['category'] ?? null) === 'Package') {
            $items    = (array) $request->input('item_id', []);
            $qtys     = (array) $request->input('quantity', []);
            $unitIds  = (array) $request->input('unit_id', []);
            $prices   = (array) $request->input('item_price', []);

            foreach ($items as $i => $itemId) {
                if ($itemId === null || $itemId === '') continue;

                $qty   = $qtys[$i]    ?? null;
                $unit  = $unitIds[$i] ?? null;
                $price = $prices[$i]  ?? null;

                if ($qty === null || $qty === '' || $unit === null || $unit === '' || $price === null || $price === '') {
                    return back()->withErrors(['package' => 'Each package row must include Item, Qty, Unit, and Item Price. (Row '.($i+1).')'])->withInput();
                }
                if (!is_numeric($qty) || $qty < 0)   return back()->withErrors(['package' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                if (!is_numeric($price) || $price < 0) return back()->withErrors(['package' => 'Item Price must be a non-negative number. (Row '.($i+1).')'])->withInput();

                $packageRows[] = [
                    'item_id'  => (int) $itemId,
                    'quantity' => (float) $qty,
                    'unit_id'  => (int) $unit,
                    'price'    => (float) $price,
                    'idx'      => $i,
                ];
            }

            if (count($packageRows) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }

            $itemIds  = collect($packageRows)->pluck('item_id')->unique()->values();
            $unitIdsC = collect($packageRows)->pluck('unit_id')->unique()->values();

            $itemNamesById = Poojaitemlists::whereIn('id', $itemIds)->pluck('item_name', 'id');
            $unitNamesById = PoojaUnit::whereIn('id', $unitIdsC)->pluck('unit_name', 'id');

            if ($itemNamesById->count() !== $itemIds->count())  return back()->withErrors(['item_id' => 'One or more selected items were not found.'])->withInput();
            if ($unitNamesById->count() !== $unitIdsC->count()) return back()->withErrors(['unit_id' => 'One or more selected units were not found.'])->withInput();

            $packageRows = array_map(function ($row) use ($itemNamesById, $unitNamesById) {
                $row['item_name'] = (string) ($itemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($unitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $packageRows);
        }

        // 1b) Build & validate SUBSCRIPTION ITEM rows
        $subscriptionRows = [];
        if (($validated['category'] ?? null) === 'Subscription') {
            $sItems   = (array) $request->input('sub_item_id', []);
            $sQtys    = (array) $request->input('sub_quantity', []);
            $sUnitIds = (array) $request->input('sub_unit_id', []);
            $sPrices  = (array) $request->input('sub_item_price', []);

            foreach ($sItems as $i => $itemId) {
                if ($itemId === null || $itemId === '') continue;

                $qty   = $sQtys[$i]    ?? null;
                $unit  = $sUnitIds[$i] ?? null;
                $price = $sPrices[$i]  ?? null;

                if ($qty === null || $qty === '' || $unit === null || $unit === '' || $price === null || $price === '') {
                    return back()->withErrors(['subscription_items' => 'Each subscription row must include Item, Qty, Unit, and Item Price. (Row '.($i+1).')'])->withInput();
                }
                if (!is_numeric($qty) || $qty < 0)   return back()->withErrors(['subscription_items' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                if (!is_numeric($price) || $price < 0) return back()->withErrors(['subscription_items' => 'Item Price must be a non-negative number. (Row '.($i+1).')'])->withInput();

                $subscriptionRows[] = [
                    'item_id'  => (int) $itemId,
                    'quantity' => (float) $qty,
                    'unit_id'  => (int) $unit,
                    'price'    => (float) $price,
                    'idx'      => $i,
                ];
            }

            // Optional: require at least one row when Subscription selected
            if (count($subscriptionRows) < 1) {
                return back()->withErrors(['sub_item_id' => 'Please add at least one subscription item.'])->withInput();
            }

            $sItemIds  = collect($subscriptionRows)->pluck('item_id')->unique()->values();
            $sUnitIdsC = collect($subscriptionRows)->pluck('unit_id')->unique()->values();

            $sItemNamesById = Poojaitemlists::whereIn('id', $sItemIds)->pluck('item_name', 'id');
            $sUnitNamesById = PoojaUnit::whereIn('id', $sUnitIdsC)->pluck('unit_name', 'id');

            if ($sItemNamesById->count() !== $sItemIds->count())  return back()->withErrors(['sub_item_id' => 'One or more selected subscription items were not found.'])->withInput();
            if ($sUnitNamesById->count() !== $sUnitIdsC->count()) return back()->withErrors(['sub_unit_id' => 'One or more selected subscription units were not found.'])->withInput();

            $subscriptionRows = array_map(function ($row) use ($sItemNamesById, $sUnitNamesById) {
                $row['item_name'] = (string) ($sItemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($sUnitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $subscriptionRows);
        }

        // 2) Handle image
        $imageUrl = null;
        if ($request->hasFile('product_image')) {
            $hashName = $request->file('product_image')->hashName();
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $imageUrl = asset('product_images/' . $hashName);
        }

        // 3) Derivations
        $isFlower = (($validated['category'] ?? null) === 'Flower');
        $isSub    = (($validated['category'] ?? null) === 'Subscription');

        $productId = $isFlower ? 'FLOW' . mt_rand(1000000, 9999999) : 'PRODUCT' . mt_rand(1000000, 9999999);
        $slug      = Str::slug($validated['name'], '-');

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
            $malaProvidedBool, $isFlowerAvailableBool, $isFlower, $isSub,
            $packageRows, $subscriptionRows
        ) {
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

            // Save pooja_id only if Package; null otherwise
            $product->pooja_id      = ($validated['category'] === 'Package') ? $request->input('pooja_id', null) : null;

            // Stock & duration
            $product->stock         = $isFlower ? null : ($request->filled('stock') ? (int)$request->input('stock') : 0);
            $product->duration      = $isSub ? $request->input('duration', null) : null;

            // Single per-day price for Subscription
            $product->per_day_price = $isSub ? (float) $request->input('per_day_price', 0) : null;

            $product->benefits      = $benefitString;
            $product->product_image = $imageUrl;

            // Flower-only
            $product->mala_provided       = $malaProvidedBool;       // bool|null
            $product->is_flower_available = $isFlowerAvailableBool;  // bool|null
            $product->available_from      = $isFlower ? $request->input('available_from') : null;
            $product->available_to        = $isFlower ? $request->input('available_to')   : null;

            $product->save();

            // PACKAGE items
            if (($validated['category'] ?? null) === 'Package' && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'], // save unit name
                        'price'      => $row['price'],
                    ]);
                }
            }

            // SUBSCRIPTION items
            if ($isSub && !empty($subscriptionRows)) {
                foreach ($subscriptionRows as $row) {
                    SubscriptionItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'], // save unit name
                        'price'      => $row['price'],
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $product = FlowerProduct::findOrFail($id);

        // 1) Load ALL items & units so previously saved rows always appear in <select>
        $Poojaitemlist = Poojaitemlists::orderBy('item_name')
            ->get(['id','item_name']);

        $pooja_units = PoojaUnit::orderBy('unit_name')
            ->get(['id','unit_name']);

        // 2) Old rows saved as text names
        $rows = PackageItem::where('product_id', $product->product_id)
            ->get(['item_name','unit','quantity','price']);

        // 3) Build normalized lookup maps: name -> id
        $itemIdByName = $Poojaitemlist
            ->reduce(function ($carry, $r) {
                $carry[mb_strtolower(trim($r->item_name))] = (int) $r->id;
                return $carry;
            }, []);

        $unitIdByName = $pooja_units
            ->reduce(function ($carry, $r) {
                $carry[mb_strtolower(trim($r->unit_name))] = (int) $r->id;
                return $carry;
            }, []);

        // 4) Map text -> IDs for the form (and keep original labels as fallback)
        $packageItems = $rows->map(function ($r) use ($itemIdByName, $unitIdByName) {
            $itemKey = mb_strtolower(trim((string) $r->item_name));
            $unitKey = mb_strtolower(trim((string) $r->unit));

            return [
                'item_id'     => $itemIdByName[$itemKey] ?? null,
                'quantity'    => $r->quantity,
                'unit_id'     => $unitIdByName[$unitKey] ?? null,
                'price'       => $r->price,
                // Keep originals for a graceful fallback option if mapping fails
                'item_label'  => (string) $r->item_name,
                'unit_label'  => (string) $r->unit,
            ];
        })->values()->toArray();

        return view('admin.edit-product', compact(
            'product',
            'Poojaitemlist',
            'pooja_units',
            'packageItems'
        ));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = FlowerProduct::findOrFail($id);

        // 1) Validate (no variants)
        $validated = $request->validate([
            'name'          => ['required','string','max:255'],
            'odia_name'     => ['nullable','string','max:255'],
            'mrp'           => ['required','numeric','min:0'],
            'price'         => ['required','numeric','min:0','lte:mrp'],
            'discount'      => ['nullable','numeric','min:0'],
            'category'      => ['required', Rule::in(['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books'])],
            'stock'         => ['nullable','integer','min:0'],
            'duration'      => ['nullable','required_if:category,Subscription','in:1,3,6'],
            'product_image' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:10000'],
            'description'   => ['required','string'],

            // Package rows (IDs come from the Blade selects)
            'item_id'       => ['nullable','array'],
            'item_id.*'     => ['nullable','integer'],
            'quantity'      => ['nullable','array'],
            'quantity.*'    => ['nullable','numeric','min:0'],
            'unit_id'       => ['nullable','array'],
            'unit_id.*'     => ['nullable','integer'],
            'item_price'    => ['nullable','array'],
            'item_price.*'  => ['nullable','numeric','min:0'],

            // Benefits
            'benefits'      => ['nullable','array'],
            'benefits.*'    => ['nullable','string','max:255'],

            // Flower-only
            'mala_provided'    => ['nullable','required_if:category,Flower','in:yes,no'],
            'flower_available' => ['nullable','required_if:category,Flower','in:yes,no'],
            'available_from'   => ['nullable','required_if:flower_available,yes','date'],
            'available_to'     => ['nullable','required_if:flower_available,yes','date','after_or_equal:available_from'],

            // Package-only
            'pooja_id'         => ['nullable','integer'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // 2) Build package rows (only if category is Package)
        $packageRows = [];
        if (($validated['category'] ?? null) === 'Package') {
            $items   = (array) $request->input('item_id', []);
            $qtys    = (array) $request->input('quantity', []);
            $unitIds = (array) $request->input('unit_id', []);
            $prices  = (array) $request->input('item_price', []);

            foreach ($items as $i => $itemId) {
                if ($itemId === null || $itemId === '') continue;

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
                    'item_id'  => (int) $itemId,
                    'quantity' => (float) $qty,
                    'unit_id'  => (int) $unit,
                    'price'    => (float) $price,
                ];
            }

            if (count($packageRows) < 1) {
                return back()->withErrors(['item_id' => 'Please add at least one package item.'])->withInput();
            }

            // Resolve names now (we store names, not IDs)
            $itemIds  = collect($packageRows)->pluck('item_id')->unique()->values();
            $unitIdsC = collect($packageRows)->pluck('unit_id')->unique()->values();

            $itemNamesById = Poojaitemlists::whereIn('id', $itemIds)->pluck('item_name', 'id');
            $unitNamesById = PoojaUnit::whereIn('id', $unitIdsC)->pluck('unit_name', 'id');

            if ($itemNamesById->count() !== $itemIds->count()) {
                return back()->withErrors(['item_id' => 'One or more selected items were not found.'])->withInput();
            }
            if ($unitNamesById->count() !== $unitIdsC->count()) {
                return back()->withErrors(['unit_id' => 'One or more selected units were not found.'])->withInput();
            }

            $packageRows = array_map(function ($row) use ($itemNamesById, $unitNamesById) {
                $row['item_name'] = (string) ($itemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($unitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $packageRows);
        }

        // 3) Persist
        DB::transaction(function () use ($request, $validated, $product, $packageRows) {
            $isFlower       = ($validated['category'] === 'Flower');
            $isSubscription = ($validated['category'] === 'Subscription');
            $isPackage      = ($validated['category'] === 'Package');

            // Benefits to "#" string
            $benefitString = null;
            if (!empty($validated['benefits'])) {
                $cleaned = array_filter(array_map('trim', $validated['benefits']));
                $benefitString = $cleaned ? implode('#', $cleaned) : null;
            }

            // Slug if name changed
            if ($product->name !== $validated['name']) {
                $product->slug = Str::slug($validated['name'], '-');
            }

            // Flower radio -> booleans
            $malaProvidedBool    = null;
            $flowerAvailableBool = null;
            if ($isFlower) {
                $malaProvidedBool    = $request->filled('mala_provided')    ? $request->mala_provided === 'yes'    : null;
                $flowerAvailableBool = $request->filled('flower_available') ? $request->flower_available === 'yes' : null;
            }

            // Assign main fields
            $product->name        = $validated['name'];
            $product->odia_name   = $validated['odia_name'] ?? null;
            $product->price       = $validated['price'];
            $product->mrp         = $validated['mrp'];
            $product->discount    = $validated['discount'] ?? null;
            $product->description = $validated['description'];
            $product->category    = $validated['category'];

            // Stock & Duration
            $product->stock    = $isFlower ? null : ($request->input('stock') !== null ? (int)$request->input('stock') : ($product->stock ?? 0));
            $product->duration = $isSubscription ? $request->input('duration') : null;

            // Package-specific meta
            $product->pooja_id = $isPackage ? $request->input('pooja_id', null) : null;

            // Benefits & Flower-only
            $product->benefits              = $benefitString;
            $product->mala_provided         = $malaProvidedBool;
            $product->is_flower_available   = $flowerAvailableBool;
            $product->available_from        = $isFlower ? $request->input('available_from') : null;
            $product->available_to          = $isFlower ? $request->input('available_to')   : null;

            // Image upload (replace old)
            if ($request->hasFile('product_image')) {
                if ($product->product_image && file_exists(public_path('product_images/' . basename($product->product_image)))) {
                    @unlink(public_path('product_images/' . basename($product->product_image)));
                }
                $hashName = $request->file('product_image')->hashName();
                $request->file('product_image')->move(public_path('product_images'), $hashName);
                // store absolute URL because your Blade prints {{ $product->product_image }}
                $product->product_image = asset('product_images/' . $hashName);
            }

            $product->save();

            // Rebuild package rows
            PackageItem::where('product_id', $product->product_id)->delete();

            if ($isPackage && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],
                    ]);
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
        $products = FlowerProduct::where('status', 'active')
            ->with(['pooja', 'packageItems']) // load pooja + new packageItems only
            ->get();

        return view('admin.manage-product', compact('products'));
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
