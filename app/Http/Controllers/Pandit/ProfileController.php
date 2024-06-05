<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Career;
use App\Models\PanditIdCard;
use App\Models\PanditEducation;
use App\Models\PanditVedic;

class ProfileController extends Controller
{
     //Pandit Profile section
     public function panditprofiles(){
        $languages = [
            'English','Odia','Hindi','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
            'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
            'Sanskrit', 'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        return view("panditprofile", compact('languages'));
    }

    public function saveprofile(Request $request)
    {
        $request->validate([
   
            'profile_photo' => 'nullable|image|max:2048', // Ensure it's an image file
        ]);

        $profile = new Profile();

        $profile->profile_id = $request->profile_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
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
    public function manageprofile(){

        $pandit_profile = Profile::latest()->first();

        // Example array of languages
        $languages = [
            'English', 'Odia', 'Hindi', 'Sanskrit', 'Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 
            'Kashmiri', 'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi', 
            'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        
        $pandit_profile->language = explode(',', $pandit_profile->language);

        $pandit_career = Career::latest()->first();
        $pandit_idcards = PanditIdCard::where('status', 'active')->get();
        $pandit_educations = PanditEducation::where('status', 'active')->get();
        $pandit_vedics = PanditVedic::where('status', 'active')->get();

        return view('pandit/manageprofile', compact('pandit_profile', 'languages','pandit_career','pandit_idcards','pandit_educations','pandit_vedics'));
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
        $filename = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/profile_photo'), $filename);
        $profile->profile_photo = $filename;
    }

        if ($profile->save()) {
            return redirect()->route('/pandit/poojaitemlist')->with('success', 'Data updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update data.');
        } 
    }
   
    
}
