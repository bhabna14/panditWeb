<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdcardDetail;
use App\Models\Career;
use App\Models\EduDetail;
use App\Models\VedicDetail;

class CareerController extends Controller
{
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
        $career->career_id = $request->career_id;
        $career->qualification = $request->qualification;
        $career->total_experience = $request->experience;
        $career->save();

        $idDetails = [];
        $eduDetails = [];
        $vedicDetails = [];

        // Pandit Career Photo Upload
        foreach ($request->id_type as $key => $id_type) {
            $file = $request->file('upload_id')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/id_proof'), $fileName);

            // Save form data to the database
            $iddata = new IdcardDetail();
            $iddata->career_id = $career->career_id;
            $iddata->id_type = $id_type;
            $iddata->upload_id = $fileName; // Save file path in the database
            $iddata->save();

            $idDetails[] = $iddata;
        }

        // Pandit Education Photo Upload
        foreach ($request->education_type as $key => $education_type) {
            $file = $request->file('upload_edu')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/edu_details'), $fileName);

            // Save form data to the database
            $edudata = new EduDetail();
            $edudata->career_id = $career->career_id;
            $edudata->education_type = $education_type;
            $edudata->upload_education = $fileName; // Save file path in the database
            $edudata->save();

            $eduDetails[] = $edudata;
        }

        // Pandit Vedic Photo Upload
        foreach ($request->vedic_type as $key => $vedic_type) {
            $file = $request->file('upload_vedic')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/vedic_details'), $fileName);

            // Save form data to the database
            $vedicdata = new VedicDetail();
            $vedicdata->career_id = $career->career_id;
            $vedicdata->vedic_type = $vedic_type;
            $vedicdata->upload_vedic = $fileName; // Save file path in the database
            $vedicdata->save();

            $vedicDetails[] = $vedicdata;
        }

        return response()->json([
            'message' => 'Profile created successfully',
            'career' => $career,
            'idDetails' => $idDetails,
            'eduDetails' => $eduDetails,
            'vedicDetails' => $vedicDetails,
        ], 201);
    }

}
