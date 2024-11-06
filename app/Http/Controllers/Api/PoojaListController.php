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
use App\Models\Rating;
use App\Models\PoojaUnit;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;


class PoojaListController extends Controller
{
    public function AllPoojaList()
    {
        try {
            $today = Carbon::today()->toDateString();

            $pandit = Auth::guard('sanctum')->user();
        
            $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();
            
            $selectedPoojas = Poojaskill::where('pandit_id', $pandit->pandit_id)
                                        ->where('status', 'active')
                                        ->get();
    
            $selectedPoojaIds = $selectedPoojas->pluck('pooja_id')->toArray();
    
            $all_Pooja_Lists = Poojalist::where('status', 'active')
                                        ->whereNotIn('id', $selectedPoojaIds)
                                        ->get()
                                        ->map(function ($pooja) {
                                            $pooja->pooja_photo_url = asset('assets/img/'.$pooja->pooja_photo); // Generate full URL for the photo
                                            return $pooja;
                                        });
            
            // Add the photo URLs to selected poojas
            $selectedPoojas = $selectedPoojas->map(function ($pooja) {
                $pooja->pooja_photo_url = asset('assets/img/' . $pooja->pooja_photo); // Generate full URL for the photo
                return $pooja;
            });
            
           // Fetch pooja requests
           $pooja_requests = Booking::with(['user', 'pooja', 'address']) // Load relationships
           ->where('pandit_id', $pandit_details->id)
           ->where('application_status', 'pending')
           ->orderBy('created_at', 'desc')
           ->get()->map(function ($booking) {
               $booking->pooja->pooja_photo_url = asset('assets/img/' . $booking->pooja->pooja_photo); // Generate full URL for the pooja photo
               return $booking;
           });
   
           // Fetch today's pooja
           $today_pooja = Booking::with(['user', 'pooja', 'address'])
           ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
           ->where('bookings.pandit_id', $pandit_details->id)
           ->where('bookings.payment_status', 'paid')
           ->where('bookings.pooja_status','!=', 'completed')
           ->whereDate('bookings.booking_date', $today)
           ->orderBy('bookings.booking_date', 'asc')
           ->select('bookings.*', 'pooja_list.pooja_name as pooja_name', 'pooja_list.pooja_photo as pooja_photo')
           ->get()->map(function ($booking) {
               $booking->pooja_photo_url = asset('assets/img/' . $booking->pooja_photo); // Generate full URL for the pooja photo
               return $booking;
           });       
    
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
            // Find the existing PoojaItem by ID
            $poojaItem = PoojaItems::findOrFail($id);
    
            // Update the status to 'deleted'
            $poojaItem->status = 'deleted';
    
            // Save the changes to the database
            if ($poojaItem->save()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Data status deleted successfully.'
                ], 200);
            } else {
                return response()->json(['message' => 'Failed to update data status.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating pooja item status.',
                'error' => $e->getMessage()
            ], 500);
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
    public function updatePoojaitem(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'pooja_id' => 'required|integer',
                'pooja_name' => 'required|string',
                'item_id' => 'required|integer',
                'variant_id' => 'required|integer'
            ]);

            // Find the existing PoojaItem by ID
            $poojaItem = PoojaItems::findOrFail($id);

            // Assign the validated data to variables
            $poojaItem->pooja_id = $validatedData['pooja_id'];
            $poojaItem->pooja_name = $validatedData['pooja_name'];
            $poojaItem->item_id = $validatedData['item_id'];
            $poojaItem->variant_id = $validatedData['variant_id'];
            $poojaItem->pandit_id = Auth::guard('sanctum')->user()->pandit_id; // Assuming you want to link this to the authenticated pandit

            // Save the updated data to the database
            if ($poojaItem->save()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Data updated successfully.'
                ], 200);
            } else {
                return response()->json(['message' => 'Failed to update data.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating pooja items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    // public function approvedPoojaList()
    // {
    //     try {
    //         $pandit = Auth::guard('sanctum')->user();

    //         // Fetch pandit details
    //         $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

    //         // Fetch approved Pooja
    //         $approved_pooja = Booking::with(['user', 'pooja', 'address'])
    //             ->where('pandit_id', $pandit_details->id)
    //             // ->where('status','!=', 'pending')
    //             ->orderBy('created_at', 'desc')
    //             ->get()
    //             ->map(function ($booking) {
    //                 $booking->pooja->pooja_photo_url = asset('assets/img/' . $booking->pooja->pooja_photo);
    //                 return $booking;
    //             });

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Pooja list fetched successfully.',
    //             'data' => [
    //                 'approved_pooja' => $approved_pooja,
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Failed to fetch pooja list.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function approvedPoojaList()
{
    try {
        $pandit = Auth::guard('sanctum')->user();

        // Fetch pandit details
        $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

        // Fetch approved Pooja
        $approved_pooja = Booking::with(['user', 'pooja', 'address'])
            ->where('pandit_id', $pandit_details->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                // Add pooja photo URL
                $booking->pooja->pooja_photo_url = asset('assets/img/' . $booking->pooja->pooja_photo);

                // Check if a rating exists for the booking
                $rating = Rating::where('booking_id', $booking->booking_id)->first();

                if ($rating) {
                    // Add full URL for audio file and image path
                    $rating->audio_file_url = $rating->audio_file ? asset('storage/'.$rating->audio_file) : null;
                    $rating->image_path_url = $rating->image_path ? asset('storage/'.$rating->image_path) : null;

                    // Include the rating data
                    $booking->rating = $rating->toArray();
                } else {
                    $booking->rating = null;
                }

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

    

public function manageUnitApi()
{
    $poojaUnits = PoojaUnit::where('status', 'active')->get();

    if ($poojaUnits->isEmpty()) {
        return response()->json([
            'status' => 200,
            'message' => 'No active pooja units found'
        ], 200);
    }

    return response()->json([
        'status' => 200,
        'data' => $poojaUnits
    ], 200);
}

}
