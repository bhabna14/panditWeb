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
        $flowerlist  = FlowerProduct::where('status', 'active')->where('category','Flower')->get(); // variants no longer needed
        $pooja_units    = PoojaUnit::orderBy('unit_name')->get();

        return view('admin.add-product', compact('flowerlist', 'pooja_list', 'pooja_units'));
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

            // Stock
            'stock'            => ['nullable','integer','min:0'],
        ], [
            'price.lte' => 'Sale price must be less than or equal to MRP.',
            'available_to.after_or_equal' => 'The "Available To" date must be the same as or after the "Available From" date.',
        ]);

        // -------- PACKAGE rows (resolve item names from FlowerProduct where category=Flower) ----------
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
                    return back()->withErrors(['package' => 'Each package row must include Item, Qty, Unit, and Item Price. (Row '.($i+1).')'])->withInput();
                }
                if (!is_numeric($qty) || $qty < 0)     return back()->withErrors(['package' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
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

            // ✅ Use FlowerProduct names (only category=Flower)
            $itemNamesById = FlowerProduct::whereIn('id', $itemIds)
                ->where('category', 'Flower')
                ->pluck('name', 'id');

            $unitNamesById = PoojaUnit::whereIn('id', $unitIdsC)->pluck('unit_name', 'id');

            if ($itemNamesById->count() !== $itemIds->count())  {
                return back()->withErrors(['item_id' => 'One or more selected items were not found among Flower products.'])->withInput();
            }
            if ($unitNamesById->count() !== $unitIdsC->count()) {
                return back()->withErrors(['unit_id' => 'One or more selected units were not found.'])->withInput();
            }

            // Map to the exact keys we save later
            $packageRows = array_map(function ($row) use ($itemNamesById, $unitNamesById) {
                $row['item_name'] = (string) ($itemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($unitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $packageRows);
        }

        // -------- SUBSCRIPTION item rows (same source: FlowerProduct) ----------
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
                if (!is_numeric($qty) || $qty < 0)     return back()->withErrors(['subscription_items' => 'Quantity must be a non-negative number. (Row '.($i+1).')'])->withInput();
                if (!is_numeric($price) || $price < 0) return back()->withErrors(['subscription_items' => 'Item Price must be a non-negative number. (Row '.($i+1).')'])->withInput();

                $subscriptionRows[] = [
                    'item_id'  => (int) $itemId,
                    'quantity' => (float) $qty,
                    'unit_id'  => (int) $unit,
                    'price'    => (float) $price,
                    'idx'      => $i,
                ];
            }

            if (count($subscriptionRows) < 1) {
                return back()->withErrors(['sub_item_id' => 'Please add at least one subscription item.'])->withInput();
            }

            $sItemIds  = collect($subscriptionRows)->pluck('item_id')->unique()->values();
            $sUnitIdsC = collect($subscriptionRows)->pluck('unit_id')->unique()->values();

            // ✅ Use FlowerProduct names (only category=Flower)
            $sItemNamesById = FlowerProduct::whereIn('id', $sItemIds)
                ->where('category', 'Flower')
                ->pluck('name', 'id');

            $sUnitNamesById = PoojaUnit::whereIn('id', $sUnitIdsC)->pluck('unit_name', 'id');

            if ($sItemNamesById->count() !== $sItemIds->count())  {
                return back()->withErrors(['sub_item_id' => 'One or more selected subscription items were not found among Flower products.'])->withInput();
            }
            if ($sUnitNamesById->count() !== $sUnitIdsC->count()) {
                return back()->withErrors(['sub_unit_id' => 'One or more selected subscription units were not found.'])->withInput();
            }

            $subscriptionRows = array_map(function ($row) use ($sItemNamesById, $sUnitNamesById) {
                $row['item_name'] = (string) ($sItemNamesById[$row['item_id']] ?? '');
                $row['unit_name'] = (string) ($sUnitNamesById[$row['unit_id']] ?? '');
                return $row;
            }, $subscriptionRows);
        }

        // Image
        $imageUrl = null;
        if ($request->hasFile('product_image')) {
            $hashName = $request->file('product_image')->hashName();
            $request->file('product_image')->move(public_path('product_images'), $hashName);
            $imageUrl = asset('product_images/' . $hashName);
        }

        // Derivations
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

        // Persist
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

            // PACKAGE items
            if (($validated['category'] ?? null) === 'Package' && !empty($packageRows)) {
                foreach ($packageRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'], // ✅ FlowerProduct->name
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
                        'price'      => $row['price'],
                    ]);
                }
            }

            // SUBSCRIPTION items
            if ($isSub && !empty($subscriptionRows)) {
                foreach ($subscriptionRows as $row) {
                    PackageItem::create([
                        'product_id' => $product->product_id,
                        'item_name'  => $row['item_name'], // ✅ FlowerProduct->name
                        'quantity'   => $row['quantity'],
                        'unit'       => $row['unit_name'],
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

        // 1) Load ALL items & units for selects
        $Poojaitemlist = FlowerProduct::orderBy('name')->where('category','Flower')->get(['id','name']);
        $pooja_units   = PoojaUnit::orderBy('unit_name')->get(['id','unit_name']);

        // 2) Existing line rows (names stored in PackageItem for both Package & Subscription)
        $rows = PackageItem::where('product_id', $product->product_id)
            ->get(['item_name','unit','quantity','price']);

        // 3) Fast lookup maps (normalized)
        $itemIdByName = $Poojaitemlist->reduce(function ($carry, $r) {
            $carry[mb_strtolower(trim($r->item_name))] = (int) $r->id;
            return $carry;
        }, []);
        $unitIdByName = $pooja_units->reduce(function ($carry, $r) {
            $carry[mb_strtolower(trim($r->unit_name))] = (int) $r->id;
            return $carry;
        }, []);

        // Helper: normalize a label for lookup
        $norm = fn($v) => mb_strtolower(trim((string) $v));

        // 4) Map text -> IDs for the form (keep original labels and mark not_found)
        $packageItems = $rows->map(function ($r) use ($itemIdByName, $unitIdByName, $norm) {
            $itemKey = $norm($r->item_name);
            $unitKey = $norm($r->unit);

            $itemId = $itemIdByName[$itemKey] ?? null;
            $unitId = $unitIdByName[$unitKey] ?? null;

            return [
                'item_id'     => $itemId,
                'quantity'    => $r->quantity,
                'unit_id'     => $unitId,
                'price'       => $r->price,

                // Fallback display labels
                'item_label'  => (string) $r->item_name,
                'unit_label'  => (string) $r->unit,

                // Flags to improve UI when mapping fails
                'item_not_found' => $itemId === null && strlen(trim((string)$r->item_name)) > 0,
                'unit_not_found' => $unitId === null && strlen(trim((string)$r->unit)) > 0,
            ];
        })->values()->toArray();

        return view('admin.edit-product', compact(
            'product',
            'Poojaitemlist',
            'pooja_units',
            'packageItems'
        ));
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
            ->where('status', 'active')
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
