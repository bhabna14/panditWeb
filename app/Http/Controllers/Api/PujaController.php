<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;


class PujaController extends Controller
{
    //
    public function poojalists(){
        $poojalists = Poojalist::where('status', 'active')->get();
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

    public function homepage() {
        // Get all active poojas
        $poojalists = Poojalist::where('status', 'active')->get();
        foreach ($poojalists as $poojalist) {
            $poojalist->pooja_img_url = asset('assets/img/' . $poojalist->pooja_photo);
        }
    
        // Get upcoming poojas
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(8)
                        ->get();
    
        foreach ($upcomingPoojas as $upcomingPooja) {
            $upcomingPooja->pooja_img_url = asset('assets/img/' . $upcomingPooja->pooja_photo);
        }
    
        // Check if both lists are empty
        if ($poojalists->isEmpty() && $upcomingPoojas->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
    
        // Prepare the combined data
        $data = [
            'all_active_poojas' => $poojalists,
            'upcoming_poojas' => $upcomingPoojas
        ];
    
        // Return the combined response
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    
}
