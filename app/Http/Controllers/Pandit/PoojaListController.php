<?php

namespace App\Http\Controllers\pandit;

use App\Models\Poojaitems;
use App\Models\Profile;
use App\Models\Poojaskill;
use Illuminate\Http\Request;
use App\Models\Poojaitemlists;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PoojaListController extends Controller
{
    public function poojaitemlist(){

        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $Poojaskills = Poojaskill::where('status', 'active')->where('pandit_id',$panditId)->get();

        $Poojaitemlist = Poojaitemlists::where('status', 'active')->pluck('item_name');
        // $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();

        return view('/pandit/poojaitemlist', compact('Poojaskills','Poojaitemlist'));
    }


    public function singlepoojaitem(Request $request)
    {
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $pooja_id = $request->query('pooja_id');
        
        $poojaname = Poojaskill::where('id', $pooja_id)->where('pandit_id',$panditId)->first();

        // $Poojaitemlist = Poojaitemlists::where('status', 'active')->pluck('item_name');
         $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();

        if (!$poojaname) {
            return redirect()->back()->with('error', 'Pooja not found.');
        }

        // Assuming you want to pass the pooja to a view
        return view('/pandit/poojaitems', compact('poojaname','Poojaitemlist'));
    }


    public function getPoojaDetails($pooja_id)
{
    try {
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $poojaItems = PoojaItems::join('poojaitem_list', 'poojaitem_list.id', '=', 'pandit_poojaitem.pooja_list')
                                ->join('variants', 'variants.id', '=', 'pandit_poojaitem.list_quantity') // Ensure the column name is correct
                                ->where('pandit_poojaitem.pooja_id', $pooja_id)
                                ->where('pandit_poojaitem.status', 'active')
                                ->where('pandit_poojaitem.pandit_id', $panditId)
                                ->select('pandit_poojaitem.*', 'poojaitem_list.item_name', 'variants.title')
                                ->get();

        if ($poojaItems->isEmpty()) {
            return response()->json(['error' => 'Pooja items not found.'], 404);
        }

        $poojaItems->each(function ($item) {
            // Ensure that `pooja_photo` exists in the selected columns or change it to a relevant column
            $item->pooja_photo_url = isset($item->pooja_photo) ? asset('storage/' . $item->pooja_photo) : asset('assets/img/default-image.jpg');
        });

        return response()->json([
            'status' => 200,
            'message' => 'Pooja details fetched successfully.',
            'poojaItems' => $poojaItems
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while fetching pooja details.',
            'error' => $e->getMessage()
        ]);
    }
}

    
    public function savePoojaItemList(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'pooja_id' => 'required|integer',
            'pooja_name' => 'required|string',
            'list_name.*' => 'required|string',
            'quantity.*' => 'required|integer',
            // 'unit.*' => 'required|string',
        ]);
    
        $profileId = Auth::guard('pandits')->user()->pandit_id;
        $poojaId = $validatedData['pooja_id'];
        $poojaName = $validatedData['pooja_name'];
        $listNames = $validatedData['list_name'];
        $quantities = $validatedData['quantity'];
        // $units = $validatedData['unit'];
    
        $duplicates = [];
        $savedItems = [];
        $processedNames = [];
    
        foreach ($listNames as $key => $listName) {
            if (in_array($listName, $processedNames)) {
                $duplicates[] = $listName;
                continue;
            }
    
            $existingItem = PoojaItems::where([
                ['pandit_id', '=', $profileId],
                ['pooja_id', '=', $poojaId],
                ['pooja_name', '=', $poojaName],
                ['pooja_list', '=', $listName]
            ])->first();
    
            if ($existingItem) {
                $duplicates[] = $listName;
                continue;
            }
    
            // Ensure $quantities[$key] exists
            $quantity = isset($quantities[$key]) ? $quantities[$key] : null;
    
            // Save each item to the database
            $poojaItem = new PoojaItems();
            $poojaItem->fill([
                'pandit_id' => $profileId,
                'pooja_id' => $poojaId,
                'pooja_name' => $poojaName,
                'pooja_list' => $listName,
                'list_quantity' => $quantity,
                // 'list_unit' => isset($units[$key]) ? $units[$key] : null,
            ]);
    
            if ($poojaItem->save()) {
                $savedItems[] = $listName;
                $processedNames[] = $listName;
            }
        }
    
        if (!empty($savedItems)) {
            return redirect()->route('poojaitemlist')->with('success', 'Pooja items saved successfully: ' . implode(', ', $savedItems));
        } else {
            return redirect()->back()->with('error', 'Failed to save any data.');
        }
    }
    

    public function deletePoojaItem($id)
    {
        try {
            $poojaItem = PoojaItems::findOrFail($id);
            $poojaItem->status = 'deleted'; // Update the status field
    
            if ($poojaItem->save()) {
                return response()->json(['success' => 'Pooja item deleted successfully.']);
            } else {
                return response()->json(['error' => 'Delet not success.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Pooja item not found.'], 404);
        }
    }

    public function updatePoojalist(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer',
        'list_name' => 'required|string|max:255',
        'list_quantity' => 'required|string|max:255',
        'unit' => 'required|string|max:255',
    ]);

    $poojaItem = PoojaItems::find($validatedData['id']);
    if ($poojaItem) {
        $poojaItem->pooja_list = $validatedData['list_name'];
        $poojaItem->list_quantity = $validatedData['list_quantity'];
        $poojaItem->list_unit = $validatedData['unit'];
        $poojaItem->save();

        return response()->json(['success' => 'Pooja item updated successfully.']);
    } else {
        return response()->json(['error' => 'Pooja item not found.'], 404);
    }
}

}
