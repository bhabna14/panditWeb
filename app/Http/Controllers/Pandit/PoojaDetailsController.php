<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojadetails;
use App\Models\Profile;


class PoojaDetailsController extends Controller
{
    public function savePoojadetails(Request $request)
    {

        $request->validate([
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image as an image file (jpeg, png, jpg, gif) with max size 2048 KB
            'video.*' => 'file|mimes:mp4,mov,avi|max:20480', // Validate video as a file (mp4, mov, avi) with max size 20480 KB
        ]);

        // Initialize a flag to track if all updates were successful
        $allSaved = true;
    
        // Get the active profile
        $profile = Profile::where('status', 'active')->first();
    
        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }
    
        // Extract profile ID
        $profileId = $profile->profile_id;
    
        // Loop through each Pooja detail submitted in the request
        foreach ($request->input('fee', []) as $poojaSkillId => $fee) {
            $duration = $request->input('duration.' . $poojaSkillId);
            $done_count = $request->input('done_count.' . $poojaSkillId);
            $pooja_name = $request->input('pooja_name.' . $poojaSkillId);
            $pooja_id = $request->input('pooja_id.' . $poojaSkillId);

            // Handle file uploads
            $image = $request->file('image.' . $poojaSkillId);
            $video = $request->file('video.' . $poojaSkillId);

            // Determine file paths
            $imagePath = $image ? $image->move(public_path('uploads/pooja_photo'), $image->getClientOriginalName()) : null;
            $videoPath = $video ? $video->move(public_path('uploads/pooja_video'), $video->getClientOriginalName()) : null;
    
            // Create or update Pooja details
            $result = Poojadetails::updateOrCreate(
                [
                    'pandit_id' => $profileId,
                    'pooja_id' => $pooja_id,
                    'pooja_name' => $pooja_name,
                    'pooja_fee' => $fee,
                    'pooja_duration' => $duration,
                    'pooja_done' => $done_count,
                    'pooja_photo' => $image ? $image->getClientOriginalName() : null,
                    'pooja_video' => $video ? $video->getClientOriginalName() : null,
                ]
            );
    
            // Check if the save was successful
            if (!$result) {
                $allSaved = false;
                break; // Exit the loop if any update fails
            }
        }
    
        // Redirect based on the save success
        if ($allSaved) {
            return redirect()->back()->with('success', 'Poojas have been updated successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to update data.']);
        }
    }
}
