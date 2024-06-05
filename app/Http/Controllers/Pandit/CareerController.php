<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdcardDetail;
use App\Models\Career;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use App\Models\PanditEducation;
use App\Models\PanditVedic;
use App\Models\PanditIdCard;
use App\Models\Profile;

class CareerController extends Controller
{
    // Pandit Career Section

    public function profilecareer(){
        return view("panditcareer");
    }
    public function managecareer(){
        $pandit_profile = Profile::latest()->first();
        $pandit_career = Career::latest()->first();
        $pandit_idcards = PanditIdCard::where('status', 'active')->get();
        $pandit_educations = PanditEducation::where('status', 'active')->get();
        $pandit_vedics = PanditVedic::where('status', 'active')->get();

        return view('pandit/managecareer', compact('pandit_profile','pandit_career','pandit_idcards','pandit_educations','pandit_vedics'));    }

    public function savecareer(Request $request)
    {
        // Validate the request
        $request->validate([
            'qualification' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
            'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'education_type.*' => 'required|string|in:10th,+2,+3,Master Degree',
            'upload_edu.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'vedic_type.*' => 'required|string|max:255',
            'upload_vedic.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $career = new Career();

        $latestProfile = Profile::latest()->first();

        $career->pandit_id = $latestProfile->profile_id;
        $career->career_id = $request->career_id;
        $career->qualification = $request->qualification;
        $career->total_experience = $request->experience;

         // Pandit Career Photo Upload

         foreach ($request->id_type as $key => $id_type) {
            $file = $request->file('upload_id')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/id_proof'), $fileName);

            // Save form data to the database
            $iddata = new IdcardDetail();
            $iddata->pandit_id = $latestProfile->profile_id;
            $iddata->id_type =  $id_type;
            $iddata->upload_id = $fileName; // Save file path in the database
            $iddata->save();
        }

        //Pandit Education Photo Upload

        foreach ($request->education_type as $key => $education_type) {
            $file = $request->file('upload_edu')[$key];

            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/edu_details'), $fileName);

            // Save form data to the database
            $edudata = new EduDetail();
            $edudata->pandit_id = $latestProfile->profile_id;
            $edudata->education_type = $education_type;
            $edudata->upload_education = $fileName; // Save file path in the database
            $edudata->save();
        }

        // Pandit Vedic Photo Upload

        
        foreach ($request->vedic_type as $key => $vedic_type) {
            $file = $request->file('upload_vedic')[$key];

            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/vedic_details'), $fileName);

            // Save form data to the database
            $vedicdata = new VedicDetail();
            $vedicdata->pandit_id = $latestProfile->profile_id;
            $vedicdata->vedic_type = $vedic_type;
            $vedicdata->upload_vedic = $fileName; // Save file path in the database
            $vedicdata->save();
        }

        if ($career->save()) {
            return redirect()->back()->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
        }
    }
    public function deletIdproof($id)
    {
            $affected = IdcardDetail::where('id', $id)->update(['status' => 'deleted']);
                        
            if ($affected) {
                return redirect()->back()->with('success', 'Data delete successfully.');
            } else {
                return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
            }
      
        }
        public function deletEducation($id)
        {
                $affected = PanditEducation::where('id', $id)->update(['status' => 'deleted']);
                            
                if ($affected) {
                    return redirect()->back()->with('success', 'Data delete successfully.');
                } else {
                    return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
                }
          
            }
            public function deletVedic($id)
            {
                    $affected = PanditVedic::where('id', $id)->update(['status' => 'deleted']);
                                
                    if ($affected) {
                        return redirect()->back()->with('success', 'Data delete successfully.');
                    } else {
                        return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
                    }
              
                }

    public function updateCareer(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'id_type' => 'nullable|array',
            'upload_id' => 'nullable|array',
            'upload_id.*' => 'file|mimes:jpg,png,pdf|max:2048',
            'education_type' => 'nullable|array',
            'upload_edu' => 'nullable|array',
            'upload_edu.*' => 'file|mimes:jpg,png,pdf|max:2048',
            'vedic_type' => 'nullable|array',
            'upload_vedic' => 'nullable|array',
            'upload_vedic.*' => 'file|mimes:jpg,png,pdf|max:2048',
        ]);

        $career = Career::findOrFail($id);

        // Update the scalar fields
        $career->qualification = $request->qualification;
        $career->total_experience = $request->experience;

         // Pandit Career Photo Upload
         if (!empty($request->id_type) && !empty($request->upload_id)) {

            foreach ($request->id_type as $key => $id_type) {
            $file = $request->file('upload_id')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/id_proof'), $fileName);

            // Save form data to the database
            $iddata = new IdcardDetail();
            $iddata->career_id = $request->career_id;
            $iddata->id_type =  $id_type;
            $iddata->upload_id = $fileName; // Save file path in the database
            $iddata->save();
        }
         }
        //Pandit Education Photo Upload
        if (!empty($request->education_type) && !empty($request->upload_edu)) {

        foreach ($request->education_type as $key => $education_type) {
            $file = $request->file('upload_edu')[$key];

            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/edu_details'), $fileName);

            // Save form data to the database
            $edudata = new EduDetail();
            $edudata->career_id = $request->career_id;
            $edudata->education_type = $education_type;
            $edudata->upload_education = $fileName; // Save file path in the database
            $edudata->save();
        }
        }
        // Pandit Vedic Photo Upload

        if (!empty($request->vedic_type) && !empty($request->upload_vedic)) {

        foreach ($request->vedic_type as $key => $vedic_type) {
            $file = $request->file('upload_vedic')[$key];

            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/vedic_details'), $fileName);

            // Save form data to the database
            $vedicdata = new VedicDetail();
            $vedicdata->career_id = $request->career_id;
            $vedicdata->vedic_type = $vedic_type;
            $vedicdata->upload_vedic = $fileName; // Save file path in the database
            $vedicdata->save();
        }
    
    }
        if ($career->save()) {
          
            return redirect()->back()->with('success', 'Data updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update data.');
        } 
    }
   
}
