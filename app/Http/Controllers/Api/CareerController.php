<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\IdcardDetail;
use App\Models\EduDetail;
use App\Models\VedicDetail;

class CareerController extends Controller
{
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

        dd("hi");

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


