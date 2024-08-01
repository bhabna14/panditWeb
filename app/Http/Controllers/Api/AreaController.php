<?php

namespace App\Http\Controllers\api;

use App\Models\Poojaarea;
use App\Models\Panditstate;
use Illuminate\Http\Request;
use App\Models\Panditvillage;
use App\Models\Panditdistrict;
use App\Models\Panditsubdistrict;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class AreaController extends Controller
{

    public function getDistrict($stateCode)
    {
        $districts = Panditdistrict::distinct()->where('stateCode', $stateCode)->get(['districtCode', 'districtName']);
        
        if ($districts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Districts not found for the provided state code',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Districts retrieved successfully',
            'data' => $districts
        ], 200);
    }
    
    public function getSubdistrict($districtCode)
    {
        $subdistricts = Panditsubdistrict::distinct()->where('districtCode', $districtCode)->get(['subdistrictCode', 'subdistrictName']);
        
        if ($subdistricts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Subdistricts not found for the provided district code',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Subdistricts retrieved successfully',
            'data' => $subdistricts
        ], 200);
    }
    
    public function getVillage($subdistrictCode)
    {
        $villages = Panditvillage::distinct()->where('subdistrictCode', $subdistrictCode)->get(['villageCode', 'villageName']);
        
        if ($villages->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Villages not found for the provided subdistrict code',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Villages retrieved successfully',
            'data' => $villages
        ], 200);
    }
  
    public function saveForm(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'state' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'village' => 'required|array',
                'village.*' => 'string|max:255', // Ensure each village is a string and has a max length
            ]);

            // Ensure the user is authenticated
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            // Create a new Poojaarea record
            $poojaArea = new Poojaarea();
            $poojaArea->pandit_id = $user->pandit_id;
            $poojaArea->state_code = $validatedData['state'];
            $poojaArea->district_code = $validatedData['district'];
            $poojaArea->subdistrict_code = $validatedData['city'];
            $poojaArea->village_code = implode(',', $validatedData['village']);

            // Save the Poojaarea record and check for success
            if ($poojaArea->save()) {
                Log::info('Pooja area created successfully.', ['data' => $validatedData]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Data saved successfully.',
                    'data' => $poojaArea
                ], 200);

            } else {
                Log::error('Failed to save pooja area.', ['data' => $validatedData]);

                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to save data.',
                    'data' => []
                ], 500);

            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error saving pooja area: ' . $e->getMessage());

            // Return a JSON error response
            return response()->json([
                'status' => 500,
                'message' => 'Failed to save data. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function manageArea()
{
    $panditId = Auth::guard('sanctum')->user()->pandit_id;

    $poojaAreas = Poojaarea::where('status', 'active')->where('pandit_id', $panditId)->get();
    $states = Panditstate::all()->keyBy('stateCode');
    $districts = Panditdistrict::all()->keyBy('districtCode');
    $subdistricts = Panditsubdistrict::all()->keyBy('subdistrictCode');
    $villages = Panditvillage::all()->keyBy('villageCode');
    
    foreach ($poojaAreas as $poojaArea) {
        $poojaArea->stateName = $states->get($poojaArea->state_code)->stateName ?? '';
        $poojaArea->districtName = $districts->get($poojaArea->district_code)->districtName ?? '';
        $poojaArea->subdistrictName = $subdistricts->get($poojaArea->subdistrict_code)->subdistrictName ?? '';
    
        $villageCodes = explode(',', $poojaArea->village_code);
        $villageNames = [];
        foreach ($villageCodes as $villageCode) {
            if ($village = $villages->get($villageCode)) {
                $villageNames[] = $village->villageName;
            }
        }
        $poojaArea->villageNames = implode(', ', $villageNames);
    }
    
    return response()->json([
        'status' => 200,
        'message' => 'Pooja areas retrieved successfully',
        'data' => $poojaAreas
    ], 200);
}


}
