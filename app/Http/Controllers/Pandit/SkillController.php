<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojaskill;
use App\Models\Poojalist;
use App\Models\Poojadetails;
use App\Models\Profile;

use DB;

class SkillController extends Controller
{
    
    public function poojaskill(){

        $Poojanames = Poojalist::where('status', 'active')->get();

        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }

        $profileId = $profile->profile_id;

        $selectedPoojas = Poojaskill::where('pandit_id', $profileId)->where('status','active')->pluck('pooja_id')->toArray();

        return view('/pandit/poojaskill', compact('Poojanames','selectedPoojas'));
    }
    
    public function managepoojaskill(){

        $Poojanames = Poojalist::where('status', 'active')->get();

        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }

        $profileId = $profile->profile_id;
        // Fetch previously selected poojas
        $selectedPoojas = Poojaskill::where('pandit_id', $profileId)->where('status','active')->pluck('pooja_id')->toArray();

        // Pass the data to the view
        return view('/pandit/managepoojaskill', compact('Poojanames', 'selectedPoojas'));
    }

    public function saveSkillPooja(Request $request)
{
    // Get the active profile
    $profile = Profile::where('status', 'active')->first();

    if (!$profile) {
        return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
    }

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
        return redirect()->route('poojadetails')->with('success', 'Pooja skills have been saved successfully.');
    } else {
        return redirect()->back()->withErrors(['danger' => 'Failed to saved some pooja skills.']);
    }
}

}
