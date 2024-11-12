<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\AppBanner;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;


class PujaController extends Controller
{
    //Homepage 

    public function homepage() {

        // Get  all banners 
        $banners = AppBanner::where('category', 'pandit')->get();
        foreach ($banners as $banner) {
            $banner->banner_img_url = asset('uploads/banner/' . $banner->banner_img);
        }
        // Get all active poojas
        $poojalists = Poojalist::where('status', 'active')
                            ->whereNull('pooja_date')->get();
        foreach ($poojalists as $poojalist) {
            $poojalist->pooja_img_url = asset('assets/img/' . $poojalist->pooja_photo);
        }
    
        // Get upcoming poojas
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->get();
    
        foreach ($upcomingPoojas as $upcomingPooja) {
            $upcomingPooja->pooja_img_url = asset('assets/img/' . $upcomingPooja->pooja_photo);
        }

        //Get 9 pandits 
        // $pandits = Profile::where('pandit_status', 'accepted')->get();

        $pandits = Profile::where('pandit_status', 'accepted')
                    ->whereHas('poojadetails', function($query) {
                        $query->where('status', 'active');
                    })
                    ->get();

        foreach ($pandits as $pandit) {
            $pandit->profile_photo = asset($pandit->profile_photo);
        }
    
        // Check if both lists are empty
        if ($poojalists->isEmpty() && $upcomingPoojas->isEmpty() && $banners->isEmpty() && $pandits->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
        // Prepare the formatted banner data
        $bannerData = [];
        foreach ($banners as $banner) {
            $bannerData[] = [
                'id' => $banner->id,
                'banner_img_url' => $banner->banner_img_url,
                'banner_img' => $banner->banner_img,
                'title_text' => $banner->title_text,
                'alt_text' => $banner->alt_text,
                'category' => $banner->category,
                'created_at' => $banner->created_at,
                'updated_at' => $banner->updated_at,
                
            ];
        }
        
        // Prepare the combined data
        $data = [
            'section_01' => [
                'name' => 'Banner',
                'id' => 1,
                'data' => $bannerData
            ],
            'section_02' => [
                'name' => 'All Poojas',
                'id' => 2,
                'data' => $poojalists
            ],
            'section_03' => [
                'name' => 'Upcoming Poojas',
                'id' => 3,
                'data' => $upcomingPoojas
            ],
            'section_04' => [
                'name' => 'Pandits',
                'id' => 4,
                'data' => $pandits
            ]
            
        ];
    
        // Return the combined response
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function manageAppBanner()
    {
        // Fetch all app banners
        $banners = AppBanner::all();

        // Optionally, add the full URL for the banner image
        foreach ($banners as $banner) {
            $banner->banner_img_url = asset('uploads/banner/' . $banner->banner_img); // Assuming banner_img is the field for the image name
        }

        // Return the banners in a JSON response
        return response()->json([
            'status' => 200,
            'data' => $banners
        ], 200);
    }


    public function poojalists(){
        $poojalists = Poojalist::where('status', 'active')->where(function($query) {
            $query->whereNull('pooja_date');
        })->get();
        foreach ($poojalists as $poojalist) {
            $poojalist->pooja_img_url = asset('assets/img/' . $poojalist->pooja_photo);
            // dd($poojalist->image_url);
           
        }
        if ($poojalists->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
        // return response()->json($poojalists);
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $poojalists
        ], 200);
    }
    public function panditlist(){
        $pandits = Profile::where('pandit_status', 'accepted')
                    ->whereHas('poojadetails', function($query) {
                        $query->where('status', 'active');
                    })
                    ->get();

        foreach ($pandits as $pandit) {
                $pandit->pandit_img_url = asset($pandit->profile_photo);
                                // dd($pandit->image_url);
                               
        }
                            if ($pandits->isEmpty()) {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'No data found',
                                    'data' => []
                                ], 404);
                            }
                            // return response()->json($pandits);
                            return response()->json([
                                'status' => 200,
                                'message' => 'Data retrieved successfully',
                                'data' => $pandits
                            ], 200);
       
     }
    public function upcomingpoojalists(){
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(8)
                        ->get();

        foreach ($upcomingPoojas as $upcomingPooja) {
            $upcomingPooja->pooja_img_url = asset('assets/img/' . $upcomingPooja->pooja_photo);
                            // dd($poojalist->image_url);
                           
            }
        if ($upcomingPoojas->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No data found',
                    'data' => []
                ], 404);
             }
             return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully',
                'data' => $upcomingPoojas
            ], 200);
    }

 
    
}
