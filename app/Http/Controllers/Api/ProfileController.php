<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Career;
use App\Models\IdcardDetail;
use App\Models\EduDetail;
use App\Models\VedicDetail;
 
class ProfileController extends Controller
{
  
    public function saveProfile(Request $request)
    {
       
        $profile = new Profile();
        $profile->profile_id = $request->profile_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;
        // $profile->language = $request->language;
 
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
 
        $profile->save();
 
        return response()->json(['message' => 'Profile created successfully', 'user' => $profile], 201);
       
    }

    public function updateProfile(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsappno' => 'required|string|max:15',
            'language.*' => 'string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profile = Profile::findOrFail($id);

        // Update the scalar fields
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;

        $pandilang = $request->input('language', []);
        if (is_array($pandilang)) {
            $langString = implode(',', $pandilang);
        } else {
            $langString = '';
        }
        $profile->language = $langString;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/profile_photo/' . $filename;
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filePath;
        }

        if ($profile->save()) {
            return response()->json(['success' => true, 'message' => 'Data updated successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update data.'], 500);
        }
    }
}