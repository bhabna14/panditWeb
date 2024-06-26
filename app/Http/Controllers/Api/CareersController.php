<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Profile;
use App\Models\IdcardDetail;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use Illuminate\Support\Facades\Log;

class CareersController extends Controller
{
    public function saveCareer(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'qualification' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
            'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'education_type.*' => 'required|string|in:10th,+2,+3,Master Degree',
            'upload_edu.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'vedic_type.*' => 'required|string|max:255',
            'upload_vedic.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        try {
            $career = new Career();

            $latestProfile = Profile::latest()->first();

            if (!$latestProfile) {
                return response()->json(['success' => false, 'message' => 'No profile found.'], 404);
            }

            $career->pandit_id = $latestProfile->profile_id;
            $career->qualification = $request->qualification;
            $career->total_experience = $request->experience;

            // Pandit Career Photo Upload
            foreach ($request->id_type as $key => $id_type) {
                $file = $request->file('upload_id')[$key];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/id_proof'), $fileName);

                // Save form data to the database
                $iddata = new IdcardDetail();
                $iddata->pandit_id = $latestProfile->profile_id;
                $iddata->id_type = $id_type;
                $iddata->upload_id = $fileName; // Save file path in the database
                $iddata->save();
            }

            // Pandit Education Photo Upload
            foreach ($request->education_type as $key => $education_type) {
                $file = $request->file('upload_edu')[$key];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/edu_details'), $fileName);

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
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/vedic_details'), $fileName);

                // Save form data to the database
                $vedicdata = new VedicDetail();
                $vedicdata->pandit_id = $latestProfile->profile_id;
                $vedicdata->vedic_type = $vedic_type;
                $vedicdata->upload_vedic = $fileName; // Save file path in the database
                $vedicdata->save();
            }

            if ($career->save()) {
                return response()->json(['success' => true, 'message' => 'Data saved successfully.'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to save data.'], 500);
            }
        } catch (\Exception $e) {
            // Log detailed error message with file and line number
            Log::error('Error saving career: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Optionally, log the request data for debugging purposes
            Log::error('Request data: ', $request->all());

            return response()->json(['success' => false, 'message' => 'An error occurred while saving data.'], 500);
        }
    }
}
