<?php

namespace App\Http\Controllers\pandit;

use App\Models\Poojaitems;
use App\Models\Profile;
use App\Models\Poojaskill;
use App\Models\Variant;
use Illuminate\Http\Request;
use App\Models\Poojaitemlists;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PoojaListController extends Controller
{

    public function getVariantTitle($itemId)
    {
        try {
            $variants = DB::table('variants')
                ->where('item_id', $itemId)
                ->select('id', 'title')
                ->get();
    
            if ($variants->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No variants found for the given item ID.'
                ]);
            }
    
            return response()->json([
                'status' => 200,
                'variants' => $variants
            ]);
        } catch (\Exception $e) {
            // Log the error details for further investigation
            \Log::error('Error fetching variants: ' . $e->getMessage());
    
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching variants.',
                'error' => $e->getMessage()
            ]);
        }
    }
    

    
    public function poojaitemlist(){

        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $Poojaskills = Poojaskill::where('status', 'active')->where('pandit_id',$panditId)->get();


        $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();

        return view('/pandit/poojaitemlist', compact('Poojaskills','Poojaitemlist'));
    }


    public function singlepoojaitem(Request $request)
    {
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $pooja_id = $request->query('pooja_id');
        
        $poojaname = Poojaskill::where('id', $pooja_id)->where('pandit_id',$panditId)->first();

         $Poojaitemlist = Poojaitemlists::with('variants')->where('status', 'active')->get();

        if (!$poojaname) {
            return redirect()->back()->with('error', 'Pooja not found.');
        }

        return view('/pandit/poojaitems', compact('poojaname','Poojaitemlist'));
    }


    public function getPoojaDetails($pooja_id)
    {
        try {
            $panditId = Auth::guard('pandits')->user()->pandit_id;
    
            $poojaItems = PoojaItems::join('poojaitem_list', 'poojaitem_list.id', '=', 'pandit_poojaitem.item_id')
                ->join('variants', 'variants.id', '=', 'pandit_poojaitem.variant_id')
                ->where('pandit_poojaitem.pooja_id', $pooja_id)
                ->where('pandit_poojaitem.status', 'active')
                ->where('pandit_poojaitem.pandit_id', $panditId)
                ->select('pandit_poojaitem.*', 'poojaitem_list.item_name', 'variants.title')
                ->get();
    
            if ($poojaItems->isEmpty()) {
                return response()->json(['error' => 'Pooja items not found.'], 404);
            }
    
            $poojaItems->each(function ($item) {
                $item->pooja_photo_url = isset($item->pooja_photo) ? asset('assets/img/' . $item->pooja_photo) : asset('assets/img/default-image.jpg');
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
    
    public function getVariants($listName)
    {
        try {
            // Fetch variants based on listName
            $variants = Variant::where('title', $listName)->get();
            
            return response()->json([
                'status' => 200,
                'message' => 'Variants fetched successfully.',
                'variants' => $variants
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching variants.',
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
            'item_id.*' => 'required|integer',
            'variant_id.*' => 'required|integer',
            // 'unit.*' => 'required|string',
        ]);
    
        $profileId = Auth::guard('pandits')->user()->pandit_id;
        $poojaId = $validatedData['pooja_id'];
        $poojaName = $validatedData['pooja_name'];
        $item_ids = $validatedData['item_id'];
        $variant_ids = $validatedData['variant_id'];
    
        $duplicates = [];
        $savedItems = [];
        $processedNames = [];
    
        foreach ($item_ids as $key => $itemId) {
            if (in_array($itemId, $processedNames)) {
                $duplicates[] = $itemId;
                continue;
            }
    
            // Check for duplicate entry
            $existingItem = PoojaItems::where([
                ['pandit_id', '=', $profileId],
                ['pooja_id', '=', $poojaId],
                ['pooja_name', '=', $poojaName],
                ['item_id', '=', $itemId],
                ['variant_id', '=', $variant_ids[$key]]
            ])->first();
    
            if ($existingItem) {
                $duplicates[] = $itemId;
                continue;
            }
    
            // Save each item to the database
            $poojaItem = new PoojaItems();
            $poojaItem->fill([
                'pandit_id' => $profileId,
                'pooja_id' => $poojaId,
                'pooja_name' => $poojaName,
                'item_id' => $itemId,
                'variant_id' => $variant_ids[$key],
                // 'list_unit' => isset($units[$key]) ? $units[$key] : null,
            ]);
    
            if ($poojaItem->save()) {
                $savedItems[] = $itemId;
                $processedNames[] = $itemId;
            }
        }
    
        if (!empty($duplicates)) {
            $duplicateItems = implode(', ', $duplicates);
            return redirect()->back()->with('error', 'Duplicate Pooja items found and skipped: ' . $duplicateItems);
        } elseif (!empty($savedItems)) {
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
    public function editPoojaItem($id)
    {
        $poojaItem = Poojaitems::findOrFail($id);
    
        // Assuming `pooja_itemlist` is the table that contains the `item_name`
        $poojaItemLists = Poojaitemlists::with('variants')->where('status', 'active')->get();

    
        $variants = Variant::where('item_id', $poojaItem->item_id)->get();
    
        return view('pandit.updatepoojaitem', compact('poojaItem', 'poojaItemLists', 'variants'));
    }
    
    
    
    // Update the Pooja item
    public function updatePoojaItem(Request $request, $id)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
        ]);
    
        $poojaItem = Poojaitems::findOrFail($id);
        $poojaItem->variant_id = $request->input('variant_id');
        $poojaItem->save();
    
        return redirect()->route('poojaitemlist', $id)->with('success', 'Pooja Item updated successfully.');
    }
    
}
