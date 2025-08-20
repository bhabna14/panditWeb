<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\Poojaitemlists;
use App\Models\PoojaUnit;
use Illuminate\Support\Str;
use App\Models\Variant;
use Illuminate\Support\Facades\Validator;

use DB;

class PujaController extends Controller
{
    //
    public function managePuja(){
        $poojalists = Poojalist::where('status', 'active')
        ->whereNotNull('pooja_date')
        ->orderBy('pooja_date', 'asc')->get();
        return view('admin/managepuja', compact('poojalists'));
    }
    public function manageSpecialPuja(){
        $poojalists = Poojalist::where('status', 'active')
        ->where(function($query) {
            $query->whereNull('pooja_date');
         })->get();
        return view('admin/managespecialpuja', compact('poojalists'));
    }
    public function addpuja(){
        return view('admin/addpuja');
    }
    public function savepuja(Request $request){
        $pujadata = new Poojalist();
        if($request->hasFile('pooja_photo')){

            $path = 'assets/img/'.$pujadata->pooja_photo;
           
           
            $file = $request->file('pooja_photo');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/img/',$filename);
            $pujadata->pooja_photo=$filename;
          }
          
       
        $pujadata->pooja_name = $request->pooja_name;
        $pujadata->slug = Str::slug($request->pooja_name, '-');
        $pujadata->short_description = $request->short_description;
        $pujadata->pooja_date = $request->pooja_date;
        $pujadata->description  = $request->description ;
        $pujadata->status  = 'active' ;
        $pujadata->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function editpooja(Poojalist $pooja)
    {
        return view('admin/editpooja', compact('pooja'));
    }

    public function updatepooja(Request $request,$pooja)
    {
        
        $pujadata = Poojalist::find($pooja);
        if($request->hasFile('pooja_photo')){

            $path = 'assets/img/'.$pujadata->pooja_photo;
           
           
            $file = $request->file('pooja_photo');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/img/',$filename);
            $pujadata->pooja_photo=$filename;
          }

          $pujadata->pooja_name = $request->pooja_name;
          $pujadata->slug = Str::slug($request->pooja_name, '-');
          $pujadata->short_description = $request->short_description;
          $pujadata->pooja_date = $request->pooja_date;
          $pujadata->description  = $request->description ;
          $pujadata->status  = 'active' ;
          $pujadata->update();

       
        return redirect()->route('managepuja')->with('success', 'Podcast updated successfully');
    }
    public function dltpooja(Request $request,$pooja)
    {
        $pujadata = Poojalist::find($pooja);
        $pujadata->status  = 'deactive' ;
          $pujadata->update();
    
                return redirect()->back()->with('success', 'Data delete successfully.');
    
    }
public function managePujaList()
    {
        // Load active items with their variants
        $items = Poojaitemlists::where('status', 'active')
            ->with(['variants:id,item_id,title,price'])
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'status']);

        // Flatten to one row per (item, variant) for your table
        $poojaitems = $items->flatMap(function ($item) {
            if ($item->variants->isEmpty()) {
                return collect([(object)[
                    'product_id'    => $item->id,
                    'item_name'     => $item->item_name,
                    'variant_id'    => null,
                    'variant_title' => '—',
                    'price'         => null,
                ]]);
            }

            return $item->variants->map(function ($v) use ($item) {
                return (object)[
                    'product_id'    => $item->id,
                    'item_name'     => $item->item_name,
                    'variant_id'    => $v->id,
                    'variant_title' => $v->title,
                    'price'         => $v->price,
                ];
            });
        });

        // Units for dropdowns
        $units = PoojaUnit::where('status', 'active')
            ->orderBy('unit_name')
            ->get(['id', 'unit_name']);

        return view('admin.managepujalist', compact('poojaitems', 'units'));
    }

    public function updateItem(Request $request)
    {
        // Normalize variant_id: treat empty string as null so validation won't fail
        $input = $request->all();
        if (!isset($input['variant_id']) || $input['variant_id'] === '') {
            unset($input['variant_id']); // remove to skip variant_id validation branch
        }

        // Validate base fields
        $baseRules = [
            'id'            => ['required', 'integer', 'exists:poojaitem_list,id'],
            'item_name'     => ['required', 'string', 'max:255'],
            'variant_title' => ['required', 'string', 'max:100'],
            'price'         => ['required', 'numeric', 'min:0'],
        ];

        $validated = Validator::make($input, $baseRules)->validate();

        DB::beginTransaction();
        try {
            // Lock and update item
            $item = Poojaitemlists::lockForUpdate()->findOrFail($validated['id']);
            $item->item_name = $validated['item_name'];
            // if you maintain slug: $item->slug = \Str::slug($validated['item_name']);
            $item->save();

            // If variant_id provided, update that variant (must belong to the item)
            if (isset($input['variant_id'])) {
                $variant = Variant::lockForUpdate()
                    ->where('id', (int)$input['variant_id'])
                    ->where('item_id', $item->id)
                    ->first();

                if (!$variant) {
                    DB::rollBack();
                    return back()->withErrors([
                        'variant_id' => 'Selected variant does not belong to this item.',
                    ])->withInput();
                }

                $variant->title = $validated['variant_title'];
                $variant->price = $validated['price'];
                $variant->save();
            } else {
                // No variant_id → create a new variant for this item
                Variant::create([
                    'item_id' => $item->id,
                    'title'   => $validated['variant_title'],
                    'price'   => $validated['price'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Pooja item updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to update item: ' . $e->getMessage(),
            ])->withInput();
        }
    }
public function saveItem(Request $request)
{
    // Basic validation
    $validated = $request->validate([
        'item_name'     => ['required', 'string', 'max:255'],
        'variant_title' => ['required', 'string', 'max:100'], // unit from dropdown
        'price'         => ['required', 'numeric', 'min:0'],
    ]);

    // Normalize inputs
    $name  = trim(preg_replace('/\s+/', ' ', $validated['item_name']));
    $unit  = trim(preg_replace('/\s+/', ' ', $validated['variant_title']));
    $price = $validated['price'];

    try {
        DB::beginTransaction();

        // Find (case-insensitive) existing item by name
        $item = Poojaitemlists::whereRaw('LOWER(item_name) = ?', [mb_strtolower($name)])->first();

        if (!$item) {
            // Create with unique slug
            $slug = Str::slug($name);
            $base = $slug;
            $i = 1;
            while (Poojaitemlists::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $item = Poojaitemlists::create([
                'item_name'    => $name,
                'slug'         => $slug,
                'product_type' => 'pooja',
                'status'       => 'active',
            ]);
        }

        // Check if this variant (unit) already exists for the item (case-insensitive)
        $variant = Variant::where('item_id', $item->id)
            ->whereRaw('LOWER(title) = ?', [mb_strtolower($unit)])
            ->lockForUpdate()
            ->first();

        if ($variant) {
            // Update price if changed
            $variant->price = $price;
            $variant->save();
            $msg = 'Pooja item exists; variant price updated successfully.';
        } else {
            // Create new variant
            Variant::create([
                'item_id' => $item->id,
                'title'   => $unit,
                'price'   => $price,
            ]);
            $msg = 'Pooja item and variant added successfully.';
        }

        DB::commit();
        return redirect()->back()->with('success', $msg);

    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()->back()->with('danger', 'Failed to save item: ' . $e->getMessage());
    }
}
    public function edititem(Poojaitemlists $item)
    {
        return view('admin/managepujalist', compact('item'));
    }
    
    public function dltitem(Request $request,$item)
    {
        $pujadata = Poojaitemlists::find($item);
        $pujadata->status  = 'deactive' ;
          $pujadata->update();
    
                return redirect()->back()->with('success', 'Data delete successfully.');
    
    }

    public function manageunit(){
        $poojaunits = PoojaUnit::where('status', 'active')->get();
        return view('admin/manageunit',compact('poojaunits'));
    }

    public function saveunit(Request $request){
        $poojaunit = new PoojaUnit();
        
        $poojaunit->unit_name = $request->unit_name;
       
        $poojaunit->status  = 'active' ;
        $poojaunit->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function editunit(PoojaUnit $unit)
    {
        return view('admin/manageunit', compact('unit'));
    }
    public function updateunit(Request $request)
    {
           
            $poojaunits = PoojaUnit::find($request->id);
            $poojaunits->unit_name = $request->unit_name;
            $poojaunits->save();

            return redirect()->back()->with('success', 'Item updated successfully');
    }
    public function dltunit(Request $request,$unit)
    {
        $poojaunit = PoojaUnit::find($unit);
        $poojaunit->status  = 'deactive' ;
          $poojaunit->update();
    
                return redirect()->back()->with('success', 'Data delete successfully.');
    
    }

}
