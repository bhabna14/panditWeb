<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojaskill;
use App\Models\Poojaitemlists;
use App\Models\PoojaItems;
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
    
   public function savePoojaDetails(){
    
   }
    

}
