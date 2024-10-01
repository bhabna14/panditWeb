<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Profile;
use App\Models\IdcardDetail;
use App\Models\PanditIdCard;
use App\Models\PanditEducation;
use App\Models\PanditVedic;
use App\Models\PanditLogin;
use App\Models\PanditTitle;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ProfileController extends Controller
{
    public function panditTitles(){
        $pandit_titles = PanditTitle::where('status', 'active')->get();
        if ($pandit_titles->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'No data found',
                'data' => []
            ], 200);
        }
        // return response()->json($pandit_titles);
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $pandit_titles
        ], 200);
    }
    public function saveProfile(Request $request)
    {
        // Retrieve the authenticated user
        $pandit = Auth::guard('sanctum')->user();
    
        if (!$pandit) {
            return response()->json(['error' => 'No authenticated user found.'], 401);
        }
    
        $profile = new Profile();
        $profile->pandit_id = $pandit->pandit_id;
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
        $profile->agree = $request->agree;
    
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

    
        $iddata = new IdcardDetail();
        $iddata->pandit_id = $pandit->pandit_id;
        $iddata->id_type = $request->id_type;
    
        // Handle ID upload
        if ($request->hasFile('upload_id')) {
            $file = $request->file('upload_id');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/id_proof'), $fileName);
            $iddata->upload_id = $fileName;
        }
    
        // Save the profile and return appropriate response
        if ($profile->save() && $iddata->save()) {
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
    
     
        $profile = Profile::where('pandit_id', $user->pandit_id)->first();
    
        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }
    
        // Update the scalar fields
       
        $profile->name = $request->name;
        $profile->slug = Str::slug($request->name, '-');
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        
        $profile->about_pandit = $request->about;
    
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
            $pandit_profile_login = PanditLogin::where('pandit_id', $profileId)->latest()->first();
            $pandit_profile = Profile::where('pandit_id', $profileId)->latest()->first();
            $pandit_career = Career::where('pandit_id', $profileId)->latest()->first();
            $pandit_idcards = PanditIdCard::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_educations = PanditEducation::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_vedics = PanditVedic::where('pandit_id', $profileId)->where('status', 'active')->get();
    
            // Set profile photo URL if it exists
            if ($pandit_profile && $pandit_profile->profile_photo) {
                $pandit_profile->profile_photo_url = asset($pandit_profile->profile_photo);
            }
    
            // Iterate through idcards to set the URL
            foreach ($pandit_idcards as $idcard) {
                if ($idcard->upload_id) {
                    $idcard->id_card_url = asset('/uploads/id_proof/' . $idcard->upload_id);
                }
            }
    
            // Return JSON response
            return response()->json([
                'status' => 200,
                'message' => 'Profile details fetched successfully.',
                'data' => [
                    'pandit_profile_login' => $pandit_profile_login,
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

    public function updatePhoto(Request $request)
    {
        // Retrieve the authenticated user's profile
        $profileId = Auth::guard('sanctum')->user()->pandit_id;
        $profile = Profile::where('pandit_id', $profileId)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        // Debugging: Check if the file is in the request
        if (!$request->hasFile('profile_photo')) {
            return response()->json([
                'success' => false,
                'message' => 'No photo uploaded. Debug: File not found in request.',
            ], 400);
        }

        // Handle the profile photo upload
        $file = $request->file('profile_photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'uploads/profile_photo/' . $filename;
        $file->move(public_path('uploads/profile_photo'), $filename);
        $profile->profile_photo = $filePath;

        if ($profile->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully.',
                'profile_photo_url' => asset($filePath),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile photo.',
            ], 500);
        }
    }
}