<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Profile;
use App\Models\PanditIdCard;
use App\Models\PanditEducation;
use App\Models\PanditVedic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


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
        $profile->slug = Str::slug($request->name, '-');
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
        $profile->about_pandit = $request->about;
    
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

    public function showProfileDetails()
    {
        try {
            $profileId = Auth::guard('sanctum')->user()->pandit_id;
    
            // Fetch data
            $pandit_profile = Profile::where('pandit_id', $profileId)->latest()->first();
            $pandit_career = Career::where('pandit_id', $profileId)->latest()->first();
            $pandit_idcards = PanditIdCard::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_educations = PanditEducation::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_vedics = PanditVedic::where('pandit_id', $profileId)->where('status', 'active')->get();
    
            // Return JSON response
            return response()->json([
                'status' => 200,
                'message' => 'Profile details fetched successfully.',
                'data' => [
                    'pandit_profile' => $pandit_profile,
                    'pandit_career' => $pandit_career,
                    'pandit_idcards' => $pandit_idcards,
                    'pandit_educations' => $pandit_educations,
                    'pandit_vedics' => $pandit_vedics,
                ]
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch Profile details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     
}