<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojaskill;
use App\Models\Profile;

use DB;

class SkillController extends Controller
{

    public function saveSkillPooja(Request $request)
    {
     
        $allSaved = true;

        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }
        $profileId = $profile->profile_id;

        foreach ($request->input('poojas', []) as $pooja) {

            if (!isset($pooja['id'])) {
                continue;
            }
            $poojaSkill = new Poojaskill([
                'pandit_id' => $profileId,
                'pooja_id' => $pooja['id'],
                'pooja_name' => $pooja['name'],
                'pooja_photo' => $pooja['image'] ?? null,
            ]);

            if (!$poojaSkill->save()) {
                $allSaved = false;
                break; 
            }
        }

        if ($allSaved) {
            return redirect()->back()->with('success', 'Poojas have been saved successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
        }
    }
}
