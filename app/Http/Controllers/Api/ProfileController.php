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
    public function saveCareer(Request $request)
    {
        // Instead of validating, manually process the inputs
        $data = $request->all();
        
        // Create a new Career instance
        $career = new Career();

        // Get the latest profile
        $latestProfile = Profile::latest()->first();

        $career->pandit_id = $latestProfile->profile_id;
        $career->qualification = $data['qualification'] ?? null;
        $career->total_experience = $data['experience'] ?? null;

        // Handle ID card uploads
            foreach ($data['id_type'] as $key => $id_type) {
                if (isset($request->file('upload_id')[$key])) {
                    $file = $request->file('upload_id')[$key];
                    $fileName = time().'_'.$file->getClientOriginalName();
                    $file->move(public_path('uploads/id_proof'), $fileName);

                    $iddata = new IdcardDetail();
                    $iddata->pandit_id = $latestProfile->profile_id;
                    $iddata->id_type = $id_type;
                    $iddata->upload_id = $fileName;
                    $iddata->save();
                }
            }
        

        // Handle Education uploads
            foreach ($data['education_type'] as $key => $education_type) {
                if (isset($request->file('upload_edu')[$key])) {
                    $file = $request->file('upload_edu')[$key];
                    $fileName = time().'_'.$file->getClientOriginalName();
                    $file->move(public_path('uploads/edu_details'), $fileName);

                    $edudata = new EduDetail();
                    $edudata->pandit_id = $latestProfile->profile_id;
                    $edudata->education_type = $education_type;
                    $edudata->upload_education = $fileName;
                    $edudata->save();
                }
            }
        

        // Handle Vedic uploads
            foreach ($data['vedic_type'] as $key => $vedic_type) {
                if (isset($request->file('upload_vedic')[$key])) {
                    $file = $request->file('upload_vedic')[$key];
                    $fileName = time().'_'.$file->getClientOriginalName();
                    $file->move(public_path('uploads/vedic_details'), $fileName);

                    $vedicdata = new VedicDetail();
                    $vedicdata->pandit_id = $latestProfile->profile_id;
                    $vedicdata->vedic_type = $vedic_type;
                    $vedicdata->upload_vedic = $fileName;
                    $vedicdata->save();
                }
            }
            if ($career->save()) {
                return response()->json(['message' => 'Data saved successfully.'], 201);
            } else {
                return response()->json(['message' => 'Failed to save data.'], 500);
            }
            }
}