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
    public function store(Request $request)
    {
        // dd("hi");
        // $request->validate([
        //     'title' => 'required|string',
        //     'name' => 'required|string',
        //     'email' => 'required|email|unique:profiles,email',
        //     'whatsappno' => 'nullable|string|max:20',
        //     'bloodgroup' => 'nullable|string|max:10',
        //     'maritalstatus' => 'nullable|string|max:255',
        //     'language' => 'nullable|string|max:255',
        //     'profile_photo' => 'nullable|image|max:2048', // Ensure it's an image file
        // ]);
 
        $profile = new Profile();
 
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
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filename;
        }
 
        $profile->save();
 
        return response()->json(['message' => 'Profile created successfully', 'user' => $profile], 201);
       
    }
    public function saveCareer(Request $request)
    {
        // Validate the request
        // $request->validate([
        //     'qualification' => 'required|string|max:255',
        //     'experience' => 'required|integer|min:0',
        //     'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
        //     'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        //     'education_type.*' => 'required|string|in:10th,+2,+3,Master Degree',
        //     'upload_edu.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        //     'vedic_type.*' => 'required|string|max:255',
        //     'upload_vedic.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        // ]);

        // dd("hi");

        $career = new Career();
        $career->career_id = $request->career_id;
        $career->qualification = $request->qualification;
        $career->total_experience = $request->experience;

        // Handle ID Proof Uploads
        foreach ($request->id_type as $key => $id_type) {
            $file = $request->file('upload_id')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/id_proof'), $fileName);

            $iddata = new IdcardDetail();
            $iddata->career_id = $request->career_id;
            $iddata->id_type =  $id_type;
            $iddata->upload_id = $fileName;
            $iddata->save();
        }

        // Handle Education Details Uploads
        foreach ($request->education_type as $key => $education_type) {
            $file = $request->file('upload_edu')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/edu_details'), $fileName);

            $edudata = new EduDetail();
            $edudata->career_id = $request->career_id;
            $edudata->education_type = $education_type;
            $edudata->upload_education = $fileName;
            $edudata->save();
        }

        // Handle Vedic Details Uploads
        foreach ($request->vedic_type as $key => $vedic_type) {
            $file = $request->file('upload_vedic')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/vedic_details'), $fileName);

            $vedicdata = new VedicDetail();
            $vedicdata->career_id = $request->career_id;
            $vedicdata->vedic_type = $vedic_type;
            $vedicdata->upload_vedic = $fileName;
            $vedicdata->save();
        }

        if ($career->save()) {
            return response()->json(['message' => 'Data saved successfully.'], 201);
        } else {
            return response()->json(['message' => 'Failed to save data.'], 500);
        }
    }
}