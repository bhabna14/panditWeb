<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Poojaskill;
use App\Models\Poojadetails;

class PoojaDetailsController extends Controller
{
    public function getPoojaDetails()
    {
        $Poojaskills = Poojaskill::where('pooja_status', 'show')
                                  ->where('status', 'active')
                                  ->get();

        return response()->json($Poojaskills);
    }

   
        public function managePoojaDetails()
        {
            $poojaDetails = Poojadetails::where('status', 'active')->get();
    
            return response()->json($poojaDetails);
        }
    
    public function savePoojadetails(Request $request)
    {
        // Get the active profile
        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return response()->json(['error' => 'No active profile found.'], 404);
        }

        // Extract profile ID
        $profileId = $profile->profile_id;

        // Initialize a flag to track if at least one new record was saved
        $atLeastOneSaved = false;

        // Check for duplicates and save new entries
        foreach ($request->input('pooja_name', []) as $poojaSkillId => $pooja_name) {
            $pooja_id = $request->input('pooja_id.' . $poojaSkillId);
            $fee = $request->input('fee.' . $poojaSkillId);
            $duration = $request->input('duration.' . $poojaSkillId);
            $done_count = $request->input('done_count.' . $poojaSkillId);

            // Handle file uploads
            $image = $request->file('image.' . $poojaSkillId);
            $video = $request->file('video.' . $poojaSkillId);

            $imagePath = null;
            $videoPath = null;

            if ($image) {
                $imagePath = 'uploads/pooja_photo/' . $image->getClientOriginalName();
                $image->move(public_path('uploads/pooja_photo'), $imagePath);
            }
            
            if ($video) {
                $videoPath = 'uploads/pooja_video/' . $video->getClientOriginalName();
                $video->move(public_path('uploads/pooja_video'), $videoPath);
            }

            // Create new Pooja details
            $poojaDetails = new Poojadetails();
            $poojaDetails->pandit_id = $profileId;
            $poojaDetails->pooja_id = $pooja_id;
            $poojaDetails->pooja_name = $pooja_name;
            $poojaDetails->pooja_fee = $fee;
            $poojaDetails->pooja_duration = $duration;
            $poojaDetails->pooja_done = $done_count;
            $poojaDetails->pooja_photo = $imagePath;
            $poojaDetails->pooja_video = $videoPath;

            // Save the new pooja details
            $poojaSkill = Poojaskill::find($poojaSkillId);
            if ($poojaSkill) {
                $poojaSkill->pooja_status = 'hide';
                $poojaSkill->save();
            }

            if ($poojaDetails->save()) {
                $atLeastOneSaved = true;
            }
        }

        if ($atLeastOneSaved) {
            return response()->json(['message' => 'Data saved successfully.'], 201);
        } else {
            return response()->json(['message' => 'Failed to save data.'], 500);
        }
        
    }

    public function updatePoojadetails(Request $request)
    {
    
        // Get the active profile
        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return response()->json(['error' => 'No active profile found.'], 404);
        }

        // Extract profile ID
        $profileId = $profile->profile_id;

        // Initialize a flag to track if at least one update was successful
        $atLeastOneUpdated = false;

        // Process each pooja detail from the form
        foreach ($request->input('pooja_name', []) as $poojaDetailId => $pooja_name) {
            // Find the corresponding Poojadetails record
            $poojaDetail = Poojadetails::findOrFail($poojaDetailId);

            // Update fields based on form inputs
            $poojaDetail->pooja_fee = $request->input('fee.' . $poojaDetailId);
            $poojaDetail->pooja_duration = $request->input('duration.' . $poojaDetailId);
            $poojaDetail->pooja_done = $request->input('done_count.' . $poojaDetailId);

            $image = $request->file('image.' . $poojaDetailId);
            $video = $request->file('video.' . $poojaDetailId);

            if ($image) {
                $imagePath = 'uploads/pooja_photo/' . $image->getClientOriginalName();
                $image->move(public_path('uploads/pooja_photo'), $imagePath);
                $poojaDetail->pooja_photo = $imagePath;
            }

            if ($video) {
                $videoPath = 'uploads/pooja_video/' . $video->getClientOriginalName();
                $video->move(public_path('uploads/pooja_video'), $videoPath);
                $poojaDetail->pooja_video = $videoPath;
            }

            // Save the updated Poojadetails record
            if ($poojaDetail->save()) {
                $atLeastOneUpdated = true;
            }
        }

        if ($atLeastOneUpdated) {
            return response()->json(['message' => 'Data updated successfully.'], 201);
        } else {
            return response()->json(['message' => 'Failed to update data.'], 500);
        }
            
    }

    
}
