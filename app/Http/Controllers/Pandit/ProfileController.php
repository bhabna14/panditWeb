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
   
            'profile_photo' => 'nullable|image|max:2048', // Ensure it's an image file
        ]);

        $profile = new Profile();

        $profile->pandit_id = Auth::guard('pandits')->user()->pandit_id;

        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->slug = Str::slug($request->name, '-');
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;

        $pandilang = $request->input('language');
 
            $langString = implode(',', $pandilang);
            $profile->language = $langString;

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/profile_photo/' . $filename;
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filePath;
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
        $profile->maritalstatus = $request->marital;
    
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
