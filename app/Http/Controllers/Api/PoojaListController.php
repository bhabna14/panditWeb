<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojaskill;
use App\Models\Poojaitemlists;
use App\Models\PoojaItems;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;


class PoojaListController extends Controller
{
    public function poojaItemList()
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
            $poojaSkills = Poojaskill::where('status', 'active')->where('pandit_id', $panditId)->get();
            $poojaItemList = Poojaitemlists::where('status', 'active')->pluck('item_name');
    
            return response()->json([
                'status' => 200,
                'message' => 'Pooja item list fetched successfully.',
                'data' => [
                    'pooja_skills' => $poojaSkills,
                    'pooja_item_list' => $poojaItemList,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja item list.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function singlePoojaItem(Request $request)
    {
        $pooja_id = $request->query('pooja_id');
        $poojaname = Poojaskill::where('id', $pooja_id)->first();
        $Poojaitemlist = Poojaitemlists::where('status', 'active')->pluck('item_name');

        if (!$poojaname) {
            return response()->json(['error' => 'Pooja not found.'], 404);
        }

        return response()->json([
            'poojaname' => $poojaname,
            'Poojaitemlist' => $Poojaitemlist
        ], 200);
    }

    public function savePoojaItemList(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'pooja_id' => 'required|integer', 
            'pooja_name' => 'required|string',
            'list_name.*' => 'required|string',
            'quantity.*' => 'required|integer',
            'unit.*' => 'required|string',
        ]);
    
        // Get the authenticated Pandit's ID
        $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
        if (!$panditId) {
            return response()->json(['error' => 'No authenticated pandit found.'], 404);
        }
    
        // Extract data from the request
        $poojaId = $validatedData['pooja_id'];
        $poojaName = $validatedData['pooja_name'];
        $listNames = $validatedData['list_name'];
        $quantities = $validatedData['quantity'];
        $units = $validatedData['unit'];
    
        $duplicates = [];
        $savedItems = [];
        $processedNames = [];
    
        // Process each item in the list
        foreach ($listNames as $key => $listName) {
            // Skip if this pooja_name is already processed within this request
            if (in_array($listName, $processedNames)) {
                $duplicates[] = $listName;
                continue;
            }
    
            // Check if the pooja_name already exists for the given pooja_id and pandit_id in the database
            $existingItem = PoojaItems::where([
                ['pandit_id', '=', $panditId],
                ['pooja_id', '=', $poojaId],
                ['pooja_name', '=', $poojaName],
                ['pooja_list', '=', $listName]
            ])->first();
    
            if ($existingItem) {
                $duplicates[] = $listName;
                continue; // Skip saving this item and move to the next one
            }
    
            // Save each item to the database
            $poojaItem = new PoojaItems();
            $poojaItem->pandit_id = $panditId;
            $poojaItem->pooja_id = $poojaId;
            $poojaItem->pooja_name = $poojaName;
            $poojaItem->pooja_list = $listName;
            $poojaItem->list_quantity = $quantities[$key];
            $poojaItem->list_unit = $units[$key];
    
            if ($poojaItem->save()) {
                $savedItems[] = $listName;
                $processedNames[] = $listName;
            }
        }
    
        if (!empty($savedItems)) {
            return response()->json(['success' => 'Pooja items saved successfully.', 'saved_items' => $savedItems]);
        } else {
            return response()->json(['error' => 'Failed to save any data.'], 500);
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
                return response()->json(['error' => 'Delete not successful.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Pooja item not found.'], 404);
        }
    }

    public function updatePoojaitem(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'list_name' => 'required|string|max:255',
            'list_quantity' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
        ]);
    
        // Get the authenticated Pandit's ID
        $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
        if (!$panditId) {
            return response()->json(['error' => 'No authenticated pandit found.'], 404);
        }
    
        // Find the Pooja item by ID and ensure it belongs to the authenticated Pandit
        $poojaItem = PoojaItems::where('pandit_id', $panditId)->find($id);
    
        if ($poojaItem) {
            $poojaItem->pooja_list = $validatedData['list_name'];
            $poojaItem->list_quantity = $validatedData['list_quantity'];
            $poojaItem->list_unit = $validatedData['unit'];
            $poojaItem->save();
    
            return response()->json(['success' => 'Pooja item updated successfully.']);
        } else {
            return response()->json(['error' => 'Pooja item not found or does not belong to the authenticated pandit.'], 404);
        }
    }
    

}
