<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Profile;
use App\Models\PanditIdCard;
use App\Models\PanditEducation;
use App\Models\PanditVedic;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class CareersController extends Controller
{
    public function saveCareer(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'qualification' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'id_proof' => 'required|array',
            'id_proof.*' => 'required|array',
            'vedic' => 'sometimes|array',
            'vedic.*' => 'sometimes|array',
        ]);
    
        try {
            $career = new Career();
            $user = Auth::guard('sanctum')->user();
    
            $career->pandit_id = $user->pandit_id;
            $career->qualification = $request->qualification;
            $career->total_experience = $request->experience;
    
            // Save ID proof details
            if ($request->has('id_proof')) {
                foreach ($request->id_proof as $idProof) {
                    foreach ($idProof as $type => $filePath) {
                        $iddata = new IdcardDetail();
                        $iddata->pandit_id = $user->pandit_id;
                        $iddata->id_type = $type;
                        $iddata->upload_id = $filePath; // Save file path in the database
                        $iddata->save();
                    }
                }
            }
    
            // Save education details
            if ($request->has('education')) {
                foreach ($request->education as $education) {
                    foreach ($education as $type => $filePath) {
                        $edudata = new EduDetail();
                        $edudata->pandit_id = $user->pandit_id;
                        $edudata->education_type = $type;
                        $edudata->upload_education = $filePath; // Save file path in the database
                        $edudata->save();
                    }
                }
            }
    
            // Save Vedic details if provided
            if ($request->has('vedic')) {
                foreach ($request->vedic as $vedic) {
                    foreach ($vedic as $type => $filePath) {
                        $vedicdata = new VedicDetail();
                        $vedicdata->pandit_id = $user->pandit_id;
                        $vedicdata->vedic_type = $type;
                        $vedicdata->upload_vedic = $filePath; // Save file path in the database
                        $vedicdata->save();
                    }
                }
            }
            if ($career->save()) {
                return response()->json(['success' => true, 'message' => 'Data updated successfully.'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update data.'], 500);
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
    
            return redirect()->back()->withErrors(['danger' => 'An error occurred while saving data.']);
        }
    }
    
}
