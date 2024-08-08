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
        $districts = Panditdistrict::distinct()
        ->where('stateCode', $stateCode)
        ->orderBy('districtCode', 'asc')
        ->get(['districtCode', 'districtName']);
        
        
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
        $subdistricts = Panditsubdistrict::distinct()
        ->where('districtCode', $districtCode)
        ->orderBy('subdistrictName', 'asc')
        ->get(['subdistrictCode', 'subdistrictName']);
        
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
        $villages = Panditvillage::distinct()
        ->where('subdistrictCode', $subdistrictCode)
        ->orderBy('villageName', 'asc')
        ->get(['villageCode', 'villageName']);
        
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
            $poojaArea->state_code = $request->input('state');
            $poojaArea->district_code = $request->input('district');
            $poojaArea->subdistrict_code = $request->input('city');
            $poojaArea->village_code = $request->input('village');
            $villageCode = $request->input('village');
    
    
            if (is_array($villageCode)) {
                $villageString =  implode(',', $validatedData['village']);
            } else {
                $villageString = $villageCode; // Assuming it's already a string if not an array
            }
    
            $poojaArea->village_code = $villageString;
    
    
    
            // Save the Poojaarea record and check for success
            if ($poojaArea->save()) {
                Log::info('Pooja area created successfully.', ['data' => $request->all()]);
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Data saved successfully.',
                    'data' => $poojaArea
                ], 200);
    
            } else {
                Log::error('Failed to save pooja area.', ['data' => $request->all()]);
    
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

public function updatePoojaArea(Request $request,$id)
{
    $pandit = Auth::guard('sanctum')->user();

    if (!$pandit) {
        return response()->json([
            'status' => 401,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    // Find the Pooja area for the authenticated Pandit
    $poojaArea = Poojaarea::where('id', $id)->first();

    if (!$poojaArea) {
        return response()->json([
            'status' => 404,
            'message' => 'Pooja area not found.'
        ], 404);
    }

    try {
        \Log::info('Updating Pooja Area', ['poojaArea' => $poojaArea]);

        $poojaArea->subdistrict_code = $request->input('subdistrict_code');

        $villageCode = $request->input('village');

        if (is_array($villageCode)) {
            $villageString =  implode(',', $validatedData['village']);
        } else {
            $villageString = $villageCode; // Assuming it's already a string if not an array
        }
        $poojaArea->village_code = $villageString;
        $poojaArea->save();

        return response()->json(['message' => 'Pooja area updated successfully.'], 200);
    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('Failed to update Pooja area', ['error' => $e->getMessage()]);

        return response()->json(['error' => 'Failed to update pooja area.'], 400);
    }
}

public function deletePoojaArea(Request $request, $id)
{
    $poojaArea = Poojaarea::find($id);

    if ($poojaArea) {
        $poojaArea->status = 'deleted';
        $poojaArea->save();
        
        return response()->json(['message' => 'Pooja area deleted successfully.'], 200);
    } else {
        return response()->json(['error' => 'Failed to delete pooja area.'], 400);
    }
}


}
