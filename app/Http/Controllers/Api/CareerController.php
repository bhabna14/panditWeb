<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Profile;
use App\Models\IdcardDetail;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class CareerController extends Controller
{
  
    public function saveCareer(Request $request)
    {
      
        // Begin transaction
        DB::beginTransaction();
    
        try {
            $user = Auth::guard('sanctum')->user();
    
            if (!$user) {
                return response()->json(['error' => 'No authenticated user found.'], 401);
            }
    
            $career = new Career();
            $career->pandit_id = $user->pandit_id;
            $career->qualification = $request->qualification;
            $career->total_experience = $request->experience;
    
            if ($career->save()) {
                // Log the save operation
                Log::info('Career data saved successfully', ['career_id' => $career->id]);
    
                // Pandit Career Photo Upload
                foreach ($request->id_type as $key => $id_type) {
                    if (isset($request->file('upload_id')[$key])) {
                        $file = $request->file('upload_id')[$key];
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->move(public_path('uploads/id_proof'), $fileName);

                        // Save form data to the database
                        $iddata = new IdcardDetail();
                        $iddata->pandit_id = $user->pandit_id;
                        $iddata->id_type = $id_type;
                        $iddata->upload_id = $fileName; // Save file path in the database
                        if (!$iddata->save()) {
                            throw new \Exception('Failed to save ID Card data');
                        }
                        Log::info('ID Card data saved successfully', ['iddata_id' => $iddata->id]);
                    } else {
                        throw new \Exception('ID file not found');
                    }
                }
    
                // Pandit Education Photo Upload
                foreach ($request->education_type as $key => $education_type) {
                    if (isset($request->file('upload_edu')[$key])) {
                        $file = $request->file('upload_edu')[$key];
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->move(public_path('uploads/edu_details'), $fileName);
    
                        // Save form data to the database
                        $edudata = new EduDetail();
                        $edudata->pandit_id = $user->pandit_id;
                        $edudata->education_type = $education_type;
                        $edudata->upload_education = $fileName; // Save file path in the database
                        if (!$edudata->save()) {
                            throw new \Exception('Failed to save Education data');
                        }
                        Log::info('Education data saved successfully', ['edudata_id' => $edudata->id]);
                    } else {
                        throw new \Exception('Education file not found');
                    }
                }
    
                // Pandit Vedic Photo Upload
                foreach ($request->vedic_type as $key => $vedic_type) {
                    if (isset($request->file('upload_vedic')[$key])) {
                        $file = $request->file('upload_vedic')[$key];
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->move(public_path('uploads/vedic_details'), $fileName);
    
                        // Save form data to the database
                        $vedicdata = new VedicDetail();
                        $vedicdata->pandit_id = $user->pandit_id;
                        $vedicdata->vedic_type = $vedic_type;
                        $vedicdata->upload_vedic = $fileName; // Save file path in the database
                        if (!$vedicdata->save()) {
                            throw new \Exception('Failed to save Vedic data');
                        }
                        Log::info('Vedic data saved successfully', ['vedicdata_id' => $vedicdata->id]);
                    } else {
                        throw new \Exception('Vedic file not found');
                    }
                }
    
                // Commit transaction
                DB::commit();
                return response()->json(['message' => 'Data saved successfully.'], 201);
            } else {
                throw new \Exception('Failed to save career data');
            }
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            Log::error('Error saving career data', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save data.', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function manageCareer()
    {
        try {

            $profileId = Auth::guard('sanctum')->user()->pandit_id;

            $pandit_profile = Profile::where('pandit_id', $profileId)->latest()->first();
            $pandit_career = Career::where('pandit_id', $profileId)->latest()->first();
            $pandit_idcards = IdcardDetail::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_educations = EduDetail::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_vedics = VedicDetail::where('pandit_id', $profileId)->where('status', 'active')->get();

            $data = [
                'pandit_profile' => $pandit_profile,
                'pandit_career' => $pandit_career,
                'pandit_idcards' => $pandit_idcards,
                'pandit_educations' => $pandit_educations,
                'pandit_vedics' => $pandit_vedics,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
