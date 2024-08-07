<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\Poojaitemlists;
use App\Models\PoojaUnit;
use Illuminate\Support\Str;
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

    public function managePujaList() {
        $poojaitems = DB::table('poojaitem_list')
            ->join('variants', 'poojaitem_list.id', '=', 'variants.product_id')
            ->select('poojaitem_list.id as product_id', 'poojaitem_list.item_name', 'variants.title as variant_title', 'variants.price')
            ->where('poojaitem_list.status', 'active')
            ->get();
    
        return view('admin/managepujalist', compact('poojaitems'));
    }

    public function saveitem(Request $request){
        $pujadata = new Poojaitemlists();
        
        $pujadata->item_name = $request->item_name;
       
        $pujadata->status  = 'active' ;
        $pujadata->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function edititem(Poojaitemlists $item)
    {
        return view('admin/managepujalist', compact('item'));
    }
    public function updateItem(Request $request)
    {
            $request->validate([
                'id' => 'required|integer',
                'item_name' => 'required|string|max:255',
            ]);

            $item = Poojaitemlists::find($request->id);
            $item->item_name = $request->item_name;
            $item->save();

            return redirect()->back()->with('success', 'Item updated successfully');
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
