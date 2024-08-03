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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;


class PoojaListController extends Controller
{
    public function AllPoojaList()
    {
        try {
            $today = Carbon::today()->toDateString();
    
            // Fetch all pooja lists with active status
            $all_Pooja_Lists = Poojalist::where('status', 'active')->get()->map(function ($pooja) {
                $pooja->pooja_photo_url = asset('assets/img/'.$pooja->pooja_photo); // Generate full URL for the photo
                return $pooja;
            });
            $pandit = Auth::guard('sanctum')->user();
    
            // Fetch pandit details
            $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();
            
            // Fetch selected poojas for the pandit
            $selectedPoojas = Poojaskill::where('pandit_id', $pandit_details->pandit_id)
                                        ->where('status', 'active')
                                        ->get();
    
            // Fetch pooja requests
            $pooja_requests = Booking::with(['user', 'pooja', 'address']) // Load relationships
            ->where('pandit_id', $pandit_details->id)
            ->where('application_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    
            // Fetch today's pooja
            $today_pooja = Booking::with(['user', 'pooja', 'address'])
            ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
            ->where('bookings.pandit_id', $pandit_details->id)
            ->where('bookings.payment_status', 'paid')
            ->where('bookings.status','!=', 'completed')
            ->whereDate('bookings.booking_date', $today)
            ->orderBy('bookings.booking_date', 'asc')
            ->select('bookings.*', 'pooja_list.pooja_name as pooja_name', 'pooja_list.pooja_photo as pooja_photo')
            ->get();       
    
            return response()->json([
                'status' => 200,
                'message' => 'Pooja list fetched successfully.',
                'data' => [
                    'all_pooja_list' => $all_Pooja_Lists,
                    'selected_pooja' => $selectedPoojas,
                    'request_pooja' => $pooja_requests,
                    'today_pooja' => $today_pooja,
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



       // done by bhabna
    public function listofitem(){
        $listofitem = Poojaitemlists::where('status', 'active')->with('variants')->get();
   
        if ($listofitem->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $listofitem
        ], 200);
    }
  

    public function poojaitemlist($pooja_id)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;

            $poojaItems = PoojaItems::join('pooja_list', 'pooja_list.id', '=', 'pandit_poojaitem.pooja_id')
                ->join('poojaitem_list', 'poojaitem_list.id', '=', 'pandit_poojaitem.item_id')
                ->join('variants', 'variants.id', '=', 'pandit_poojaitem.variant_id') // Ensure the column name is correct
                ->where('pandit_poojaitem.pooja_id', $pooja_id)
                ->where('pandit_poojaitem.status', 'active')
                ->where('pandit_poojaitem.pandit_id', $panditId)
                ->select('pandit_poojaitem.*','pooja_list.pooja_photo', 'poojaitem_list.item_name', 'variants.title')
                ->get();

            if ($poojaItems->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Pooja items not found.',
                    'data' => []
                ], 404);
            }

            $poojaItems->each(function ($item) {
                // Ensure that `pooja_photo` exists in the selected columns or change it to a relevant column
                $item->pooja_photo_url = isset($item->pooja_photo) ? asset('assets/img/' . $item->pooja_photo) : asset('assets/img/default-image.jpg');
            });

            return response()->json([
                'status' => 200,
                'message' => 'Pooja items fetched successfully.',
                'data' => $poojaItems
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching pooja items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function savePoojaItemList(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'pooja_id' => 'required|integer',
                'pooja_name' => 'required|string',
                'item_id' => 'required|integer',
                'variant_id' => 'required|integer'
            ]);
    
            $profileId = Auth::guard('sanctum')->user()->pandit_id;
    
            // Assign the validated data to variables
            $poojaId = $validatedData['pooja_id'];
            $poojaName = $validatedData['pooja_name'];
            $itemId = $validatedData['item_id'];
            $variantId = $validatedData['variant_id'];
    
            // Here you should save the data to the database
            // Assuming you have a model named PoojaItemList, you could do something like:
            $poojaItem = new PoojaItems();
            $poojaItem->pooja_id = $poojaId;
            $poojaItem->pooja_name = $poojaName;
            $poojaItem->item_id = $itemId;
            $poojaItem->variant_id = $variantId;
            $poojaItem->pandit_id = $profileId; // Assuming you want to link this to the authenticated pandit
    
            if ($poojaItem->save()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Data saved successfully.'
                
                ], 201);
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
    

    public function approvedPoojaList()
    {
        try {
            $pandit = Auth::guard('sanctum')->user();

            // Fetch pandit details
            $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

            // Fetch approved Pooja
            $approved_pooja = Booking::with(['user', 'pooja', 'address'])
                ->where('pandit_id', $pandit_details->id)
                // ->where('status','!=', 'pending')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    $booking->pooja->pooja_photo_url = asset('assets/img/' . $booking->pooja->pooja_photo);
                    return $booking;
                });

            return response()->json([
                'status' => 200,
                'message' => 'Pooja list fetched successfully.',
                'data' => [
                    'approved_pooja' => $approved_pooja,
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
    

    
}
