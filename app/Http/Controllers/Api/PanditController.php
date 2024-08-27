<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\AppBanner;
use App\Models\Profile;
use App\Models\Poojadetails;

use Illuminate\Support\Facades\Auth;

class PanditController extends Controller
{
    //
    public function singlePanditDetails($slug)
    {
        try {
            // Fetch the single pandit based on the provided slug
            $singlePandit = Profile::where('slug', $slug)->firstOrFail();

            // Fetch the related pooja details for this pandit
            $panditPujas = Poojadetails::where('pandit_id', $singlePandit->pandit_id)
                 ->where('status','active')
                ->with('poojalist') // Load the poojalist relationship
                ->get();

            // Modify the photo and video URLs for the pandit and each pooja
            $singlePandit->profile_photo = url($singlePandit->profile_photo);

            foreach ($panditPujas as $pooja) {
                $pooja->pooja_photo = url($pooja->pooja_photo);
                $pooja->pooja_video = url($pooja->pooja_video);
                $pooja->poojalist->pooja_photo = url('assets/img/'.$pooja->poojalist->pooja_photo);
            }

            // Prepare the data to return
            $data = [
                'pandit' => $singlePandit,
                'poojas' => $panditPujas,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Pandit fetched Successfully',
                'data' => $data,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Failed to fetch pandit details.',
                'data' => []
            ], 404);
        }
    }

    public function poojadetails($slug)
    {
        try {
            // Fetch the pooja based on the provided slug
            $pooja = Poojalist::where('slug', $slug)->firstOrFail();
    
            // Fetch the related Poojadetails items along with the Profile
            $panditPujas = Poojadetails::with('profile')
                ->where('status', 'active')
                ->where('pooja_id', $pooja->id)
                ->get();
    
            // Filter out pandit pujas with a null profile
            $filteredPanditPujas = $panditPujas->filter(function ($poojaDetail) {
                return !is_null($poojaDetail->profile);
            });
    
            // Modify the photo and video URLs for each pooja
            $pooja->pooja_photo = url('assets/img/' . $pooja->pooja_photo);
            foreach ($filteredPanditPujas as $poojaDetail) {
                $poojaDetail->pooja_photo = url($poojaDetail->pooja_photo);
                $poojaDetail->pooja_video = url($poojaDetail->pooja_video);
                if ($poojaDetail->profile) {
                    $poojaDetail->profile->profile_photo = url($poojaDetail->profile->profile_photo);
                }
            }
    
            // Prepare the data to return
            $data = [
                'pooja' => $pooja,
                'pandit_pujas' => $filteredPanditPujas->values(), // Convert the collection back to an array
            ];
    
            return response()->json([
                'status' => 200,
                'message' => 'Pooja details fetched successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

}
