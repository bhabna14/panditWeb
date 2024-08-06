<?php
namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojadetails;
use App\Models\Poojaskill;
use Illuminate\Support\Facades\Auth;

use App\Models\Profile;

class PoojaDetailsController extends Controller
{
    public function poojadetails(){
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $Poojaskills = Poojaskill::where('pooja_status', 'show')->where('status','active')->where('pandit_id',$panditId)->get();

        return view('/pandit/poojadetail', compact('Poojaskills'));
    } 
    public function managepoojadetails(){

        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $poojaDetails = Poojadetails::where('status', 'active')->where('pandit_id',$panditId)->get();

        return view('pandit/managepoojadetails',compact('poojaDetails'));
    }
   
    public function savePoojadetails(Request $request)
    {
        $request->validate([
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image as an image file (jpeg, png, jpg, gif) with max size 2048 KB
            'video.*' => 'file|mimes:mp4,mov,avi|max:20480', // Validate video as a file (mp4, mov, avi) with max size 20480 KB
        ]);
    
        $profileId = Auth::guard('pandits')->user()->pandit_id;
    
        // Initialize a flag to track if at least one new record was saved
        $atLeastOneSaved = false;
    
        // Check for duplicates and save new entries
        foreach ($request->input('pooja_name', []) as $poojaSkillId => $pooja_name) {
            $pooja_id = $request->input('pooja_id.' . $poojaSkillId);
            $fee = $request->input('fee.' . $poojaSkillId);
            $durationValue = $request->input('duration_value.' . $poojaSkillId);
            $durationUnit = $request->input('duration_unit.' . $poojaSkillId);
            $duration = $durationValue . ' ' . $durationUnit; // Concatenate duration value and unit
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
    
        // Redirect based on the save success
        if ($atLeastOneSaved) {
            return redirect()->route('poojaitemlist')->with('success', 'Poojas have been updated successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'No new poojas were added.']);
        }
    }
    
    public function updatePoojadetails(Request $request)
    {
        $request->validate([
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image as an image file (jpeg, png, jpg, gif) with max size 2048 KB
            'video.*' => 'file|mimes:mp4,mov,avi|max:20480', // Validate video as a file (mp4, mov, avi) with max size 20480 KB
        ]);
    
        $profileId = Auth::guard('pandits')->user()->pandit_id;
    
        // Initialize a flag to track if at least one update was successful
        $atLeastOneUpdated = false;
    
        // Process each pooja detail from the form
        foreach ($request->input('pooja_name', []) as $poojaDetailId => $pooja_name) {
            // Find the corresponding Poojadetails record
            $poojaDetail = Poojadetails::findOrFail($poojaDetailId);
    
            // Update fields based on form inputs
            $poojaDetail->pooja_fee = $request->input('fee.' . $poojaDetailId);
            
            // Concatenate duration value and unit
            $durationValue = $request->input('duration_value.' . $poojaDetailId);
            $durationUnit = $request->input('duration_unit.' . $poojaDetailId);
            $poojaDetail->pooja_duration = $durationValue . ' ' . $durationUnit;
    
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
    
        // Redirect based on update success
        if ($atLeastOneUpdated) {
            return redirect()->back()->with('success', 'Pooja details have been updated successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'No pooja details were updated.']);
        }
    }
    
}