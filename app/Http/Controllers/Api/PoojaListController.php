<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojaskill;
use App\Models\Poojaitemlists;
use App\Models\Poojaitems;
use App\Models\Poojalist;
use App\Models\Profile;
use App\Models\Booking;

use Illuminate\Support\Facades\Auth;


class PoojaListController extends Controller
{
    public function AllPoojaList()
    {
        try {
            $all_Pooja_Lists = Poojalist::where('status', 'active')->get();

            $pandit = Auth::guard('sanctum')->user();

            $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

            $selectedPoojas = Poojaskill::where('pandit_id', $pandit_details->pandit_id)
                                        ->where('status', 'active')
                                        ->get();

            $pooja_requests = Booking::with(['user', 'pooja', 'address']) // Load relationships to get user, pooja, and address details
            ->where('pandit_id', $pandit_details->id) 
            ->where('application_status', 'pending') 
            ->orderBy('created_at', 'desc')->get();
                                        

            return response()->json([
                'status' => 200,
                'message' => 'Pooja list fetched successfully.',
                'data' => [
                    'all_pooja_list' => $all_Pooja_Lists,
                    'selected_pooja' => $selectedPoojas,
                    'request_pooja' => $pooja_requests,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja list.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function savePoojaItemList(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'pooja_id' => 'required|integer', // Example validation rules, adjust as per your needs
                'pooja_name' => 'required|string',
                'list_name.*' => 'required|string',
                'quantity.*' => 'required|integer',
                'unit.*' => 'required|string',
            ]);
    
            $profileId = Auth::guard('sanctum')->user()->pandit_id;
            // Extract data from the request
            $poojaId = $validatedData['pooja_id'];
            $poojaName = $validatedData['pooja_name'];
            $listNames = $validatedData['list_name'];
            $quantities = $validatedData['quantity'];
            $units = $validatedData['unit'];
    
            $processedNames = [];
    
            // Process each item in the list
            foreach ($listNames as $key => $listName) {
                // Skip if this pooja_name is already processed within this request
                if (in_array($listName, $processedNames)) {
                    continue;
                }
    
                // Check if the pooja_name already exists for the given pooja_id and pandit_id in the database
                $existingItem = PoojaItems::where([
                    ['pandit_id', '=', $profileId],
                    ['pooja_id', '=', $poojaId],
                    ['pooja_name', '=', $poojaName],
                    ['pooja_list', '=', $listName]
                ])->first();
    
                if ($existingItem) {
                    continue; // Skip saving this item and move to the next one
                }
    
                // Save each item to the database
                $poojaItem = new PoojaItems();
                $poojaItem->pandit_id = $profileId;
                $poojaItem->pooja_id = $poojaId;
                $poojaItem->pooja_name = $poojaName;
                $poojaItem->pooja_list = $listName;
                $poojaItem->list_quantity = $quantities[$key];
                $poojaItem->list_unit = $units[$key];
    
                if ($poojaItem->save()) {
                    $processedNames[] = $listName;
                }
            }
    
            if (!empty($processedNames)) {
                return response()->json(['message' => 'Data saved successfully.'], 201);
            } else {
                return response()->json(['message' => 'Failed to save data.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving pooja items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function poojaitemlist()
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;

            $Poojaitemlist = Poojaitems::where('status', 'active')->where('pandit_id', $panditId)->get();

            return response()->json([
                'status' => 200,
                'message' => 'Pooja items fetched successfully.',
                'data' => $Poojaitemlist
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching pooja items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

}
