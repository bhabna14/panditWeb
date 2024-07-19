<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Career;
use App\Models\IdcardDetail;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
  
    public function saveProfile(Request $request)
    {
       
    
        // Retrieve the authenticated user
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'No authenticated user found.'], 401);
        }
        $profile = new Profile();
        $profile->pandit_id = $user->pandit_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;
    
        // Handle the language input
        $pandilang = $request->input('language');
        if (is_array($pandilang)) {
            $langString = implode(',', $pandilang);
        } else {
            $langString = $pandilang; // Assuming it's already a string if not an array
        }
        $profile->language = $langString;
    
        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/profile_photo/' . $filename;
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filePath;
        }
    
        // Save the profile and return appropriate response
        if ($profile->save()) {
            return response()->json(['message' => 'Profile created successfully', 'user' => $profile], 201);
        } else {
            return response()->json(['error' => 'Failed to save data.'], 500);
        }
    }
    
    
    public function updateProfile(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'No authenticated user found.'], 401);
        }
    
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsappno' => 'required|string|max:15',
            'language.*' => 'string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Find the profile belonging to the authenticated user
        $profile = Profile::where('pandit_id', $user->id)->first();
    
        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }
    
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