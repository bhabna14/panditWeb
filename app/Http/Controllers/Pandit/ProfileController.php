<?php

namespace App\Http\Controllers\Pandit;

use App\Models\Career;
use App\Models\Profile;
use App\Models\PanditTitle;
use App\Models\PanditVedic;
use App\Models\PanditIdCard;
use Illuminate\Http\Request;
use App\Models\PanditEducation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Rules\WordCount;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
     //Pandit Profile section
     public function panditprofiles(){
        // Fetch the profile of the authenticated Pandit
        $pandititle = PanditTitle::where('status', 'active')->pluck('pandit_title');

        $languages = [
            'English','Odia','Hindi','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
            'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
            'Sanskrit', 'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        return view("panditprofile", compact('languages','pandititle'));
    }

    public function saveprofile(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', 
            'whatsappno' => 'numeric|digits:10',
            'about' => 'required', // Validate the about field with max 200 words
        ]);
        

        $profile = new Profile();

        $profile->pandit_id = Auth::guard('pandits')->user()->pandit_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->slug = Str::slug($request->name, '-');
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->gotra = $request->gotra;
        $profile->bruti = $request->bruti;
        $profile->maritalstatus = $request->marital;
        $profile->about_pandit = $request->about;


        $pandilang = $request->input('language');
 
            $langString = implode(',', $pandilang);
            $profile->language = $langString;

        // Handle profile photo upload if provided
        // if ($request->hasFile('profile_photo')) {
        //     $file = $request->file('profile_photo');
        //     $filename = time() . '.' . $file->getClientOriginalExtension();
        //     $filePath = 'uploads/profile_photo/' . $filename;
        //     $file->move(public_path('uploads/profile_photo'), $filename);
        //     $profile->profile_photo = $filePath;
        // }

        if ($request->hasFile('profile_photo')) {
            try {
                // Get the file from the request
                $file = $request->file('profile_photo');
        
                // Generate a unique filename
                $filename = time() . '.' . $file->getClientOriginalExtension();
        
                // Define the file path for saving
                $filePath = 'uploads/profile_photo/' . $filename;
                
                // Move the file to the designated directory
                $file->move(public_path('uploads/profile_photo'), $filename);
        
                // Save the file path to the profile
                $profile->profile_photo = $filePath;
        
            } catch (\Exception $e) {
                // Return a user-friendly error message if something goes wrong
                return back()->withErrors(['profile_photo' => 'Failed to upload the profile photo. Please try again.']);
            }
        }
        
        
        

        if ($profile->save()) {
            return redirect("pandit/career")->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
        }
    }
    
       public function manageprofile()
    {
        // Get the authenticated user's pandit_id
        $profileId = Auth::guard('pandits')->user()->pandit_id;

        // Fetch the profile of the authenticated Pandit
        $pandit_profile = Profile::where('pandit_id', $profileId)->latest()->first();

        // Example array of languages
        $languages = [
            'English', 'Odia', 'Hindi', 'Sanskrit', 'Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 
            'Kashmiri', 'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi', 
            'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];

        // Explode languages if profile exists
        if ($pandit_profile) {
            $pandit_profile->language = explode(',', $pandit_profile->language);
        }

        // Fetch related data
        $pandit_career = Career::where('pandit_id', $profileId)->latest()->first();
        $pandit_idcards = PanditIdCard::where('pandit_id', $profileId)->where('status', 'active')->get();
        $pandit_educations = PanditEducation::where('pandit_id', $profileId)->where('status', 'active')->get();
        $pandit_vedics = PanditVedic::where('pandit_id', $profileId)->where('status', 'active')->get();

        // Return view with all data
        return view('pandit.manageprofile', compact('pandit_profile', 'languages', 'pandit_career', 'pandit_idcards', 'pandit_educations', 'pandit_vedics'));
    }

    public function updateProfile(Request $request, $id)
    {
        // Validate the request data
       $request->validate([
           
        ]);

        $profile = Profile::findOrFail($id);

        // Update the scalar fields
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->slug = Str::slug($request->name, '-');
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->gotra = $request->gotra;
        $profile->bruti = $request->bruti;
        $profile->maritalstatus = $request->marital;
        $profile->about_pandit = $request->about;

        $pandilang = $request->input('language');
 
        $langString = implode(',', $pandilang);
        $profile->language = $langString;

    if ($request->hasFile('profile_photo')) {
        $file = $request->file('profile_photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'uploads/profile_photo/' . $filename;
        $file->move(public_path('uploads/profile_photo'), $filename);
        $profile->profile_photo = $filePath;
    }
        if ($profile->save()) {
            return redirect()->back()->with('success', 'Data updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update data.');
        } 
    }
   
    
}
