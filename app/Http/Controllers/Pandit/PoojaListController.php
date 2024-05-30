<?php

namespace App\Http\Controllers\pandit;

use App\Models\Poojaitems;
use App\Models\Profile;
use App\Models\Poojaskill;
use Illuminate\Http\Request;
use App\Models\Poojaitemlists;
use App\Http\Controllers\Controller;

class PoojaListController extends Controller
{
    public function poojaitemlist(){

        $Poojaskills = Poojaskill::where('status', 'active')->get();

        return view('/pandit/poojaitemlist', compact('Poojaskills'));
    }
    public function poojaitem(Request $request)
    {
        $pooja_id = $request->query('pooja_id');
        $poojaname = Poojaskill::where('id', $pooja_id)->first();
        $Poojaitemlist = Poojaitemlists::where('status', 'active')->pluck('item_name');

        if (!$poojaname) {
            return redirect()->back()->with('error', 'Pooja not found.');
        }

        // Assuming you want to pass the pooja to a view
        return view('/pandit/poojaitems', compact('poojaname','Poojaitemlist'));
    }
    public function managepoojaitem(Request $request)
    {
        $pooja_id = $request->query('pooja_id');

        $poojaname = Poojaskill::where('pooja_id', $pooja_id)->first();

        $poojaItems = PoojaItems::where('pooja_id', $pooja_id)->where('status', 'active')->get();

        if (!$poojaItems) {
            return redirect()->back()->with('error', 'Pooja not found.');
        }

        // Assuming you want to pass the pooja to a view
        return view('/pandit/managepoojaitem', compact('poojaItems','poojaname'));
    }
    public function savePoojaItemList(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'pooja_id' => 'required|integer', // Example validation rules, adjust as per your needs
            'pooja_name' => 'required|string',
            'list_name.*' => 'required|string',
            'quantity.*' => 'required|integer',
            'unit.*' => 'required|string',
        ]);

        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }
    
        $profileId = $profile->profile_id;
    
        // Extract data from the request
        $poojaId = $validatedData['pooja_id'];
        $poojaName = $validatedData['pooja_name'];
        $listNames = $validatedData['list_name'];
        $quantities = $validatedData['quantity'];
        $units = $validatedData['unit'];

        // Process each item in the list
        foreach ($listNames as $key => $listName) {
            // Save each item to the database or perform your action
            $poojaItem = new Poojaitems();
            $poojaItem->pandit_id = $profileId;
            $poojaItem->pooja_id = $poojaId;
            $poojaItem->pooja_name = $poojaName;
            $poojaItem->pooja_list = $listName;
            $poojaItem->list_quantity = $quantities[$key];
            $poojaItem->list_unit = $units[$key];
           
        }
        if ($poojaItem->save()) {
          
            return redirect()->route('poojaitemlist')->with('success', 'Pooja items saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to Save data.');
        } 
     
    }
    public function deletePoojaItem($id)
    {
        $poojaItem = PoojaItems::findOrFail($id);

        $poojaItem->status = 'deleted'; // Assuming there's a 'status' column in your table
        
        if ($poojaItem->save()) {
            return redirect()->back()->with('success', 'Pooja item status updated to deleted.');
        } else {
            return redirect()->back()->with('error', 'Failed to Delet data.');
        } 
    }
}
