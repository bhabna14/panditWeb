<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\Profile;
use App\Models\Poojaskill;
use App\Models\Poojadetails;
use Illuminate\Support\Facades\Auth;

class PoojaSkillController extends Controller
{
    
    public function manageSkill()
    {
        try {
            $Poojanames = Poojalist::where('status', 'active')->get();
    
            $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
            $profile = Profile::where('pandit_id', $panditId)->where('status', 'active')->first();
    
            if (!$profile) {
                return response()->json(['error' => 'No active profile found.'], 404);
            }
    
            $selectedPoojas = Poojaskill::where('pandit_id', $panditId)
                                        ->where('status', 'active')
                                        ->pluck('pooja_id')
                                        ->toArray();
    
            return response()->json([
                'Poojanames' => $Poojanames,
                'selectedPoojas' => $selectedPoojas,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch Pooja skills.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

    // pooja skill save 
    
    public function saveSkillPooja(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'No authenticated user found.'], 401);
        }
    
        // Retrieve the user's active profile
        $profile = $user->profile()->where('status', 'active')->first();
    
        if (!$profile) {
            return response()->json(['error' => 'No active profile found.'], 404);
        }
    
        // Extract profile ID
        $profileId = $profile->profile_id;
    
        // Get all submitted pooja IDs
        $submittedPoojaIds = array_column($request->input('poojas', []), 'id');
    
        // Update status to 'delet' for Poojadetails entries not in the submitted pooja IDs
        Poojadetails::where('pandit_id', $profileId)
                    ->whereNotIn('pooja_id', $submittedPoojaIds)
                    ->update(['status' => 'delet']);
    
        // Update pooja_status to 'hide' and status to 'delet' for existing Poojaskill entries not in submitted pooja IDs
        Poojaskill::where('pandit_id', $profileId)
                  ->whereNotIn('pooja_id', $submittedPoojaIds)
                  ->update(['status' => 'delet', 'pooja_status' => 'hide']);
    
        $allSaved = true;
    
        foreach ($request->input('poojas', []) as $pooja) {
            if (!isset($pooja['id'])) {
                continue;
            }
    
            // Check if the pooja already exists in Poojaskill
            $poojaSkill = Poojaskill::where('pandit_id', $profileId)
                                    ->where('pooja_id', $pooja['id'])
                                    ->first();
    
            if ($poojaSkill) {
                // If the existing entry's status is 'delet', update to 'active'
                if ($poojaSkill->status === 'delet') {
                    $poojaSkill->status = 'active';
                    $poojaSkill->pooja_status = 'show';
                    $poojaSkill->save();
                } else {
                    continue; // Skip saving if pooja_id already exists and is not 'delet'
                }
            } else {
                // Create a new Poojaskill entry
                $poojaSkill = new Poojaskill([
                    'pandit_id' => $profileId,
                    'pooja_id' => $pooja['id'],
                    'pooja_name' => $pooja['name'],
                    'pooja_photo' => $pooja['image'],
                    'pooja_status' => 'show',
                    'status' => 'active'
                ]);
    
                if (!$poojaSkill->save()) {
                    $allSaved = false;
                    break;
                }
            }
    
            // Find the Poojadetails entry with the same pooja_id and update its status to active
            $poojaDetails = Poojadetails::where('pandit_id', $profileId)
                                        ->where('pooja_id', $pooja['id'])
                                        ->first();
    
            if ($poojaDetails) {
                $poojaDetails->status = 'active';
                $poojaDetails->save();
            }
        }
    
        if ($allSaved) {
            return response()->json(['message' => 'Data saved successfully.'], 201);
        } else {
            return response()->json(['message' => 'Failed to save data.'], 500);
        }
    }
    
}
