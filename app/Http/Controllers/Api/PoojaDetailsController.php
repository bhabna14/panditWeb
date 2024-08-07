<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Poojaskill;
use App\Models\Poojadetails;
use App\Models\Poojalist;
use App\Models\Poojaitems;

use Illuminate\Support\Facades\Auth;

class PoojaDetailsController extends Controller
{
  
    public function managePoojaDetails(Request $request)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;

            $poojaDetails = Poojadetails::where('status', 'active')->where('pandit_id', $panditId)->get();
            
            return response()->json([
                'status' => 200,
                'message' => 'Pooja details fetched successfully.',
                'data' => $poojaDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function savePoojadetails(Request $request)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
            // Retrieve input data
            $pooja_id = $request->input('pooja_id');
            $pooja_name = $request->input('pooja_name');
            $fee = $request->input('fee');
            $duration = $request->input('duration');
            $done_count = $request->input('done_count');
    
            // Handle file uploads
            $image = $request->file('image');
            $video = $request->file('video');
    
            $imagePath = null;
            $videoPath = null;
    
            if ($image) {
                $imagePath = 'uploads/pooja_photo/' . time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/pooja_photo'), $imagePath);
            }
    
            if ($video) {
                $videoPath = 'uploads/pooja_video/' . time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('uploads/pooja_video'), $videoPath);
            }
    
            // Fetch pooja photo from Poojalist table
            $pooja_photo = Poojalist::where('id', $pooja_id)->first();
    
            if (!$pooja_photo) {
                return response()->json(['message' => 'Pooja not found.'], 404);
            }
    
            // Create new Pooja details
            $poojaDetails = new Poojadetails();
            $poojaDetails->pandit_id = $panditId;
            $poojaDetails->pooja_id = $pooja_id;
            $poojaDetails->pooja_name = $pooja_name;
            $poojaDetails->pooja_fee = $fee;
            $poojaDetails->pooja_duration = $duration;
            $poojaDetails->pooja_done = $done_count;
            $poojaDetails->pooja_photo = $imagePath;
            $poojaDetails->pooja_video = $videoPath;
    
            // Save the new pooja details and update the pooja skill status
            if ($poojaDetails->save()) {
                $poojaSkill = new Poojaskill();
                $poojaSkill->pandit_id = $panditId;
                $poojaSkill->pooja_id = $pooja_id;
                $poojaSkill->pooja_name = $pooja_name;
                $poojaSkill->pooja_photo = $pooja_photo->pooja_photo; // Use the photo from Poojalist
                $poojaSkill->pooja_status = 'hide';
                $poojaSkill->save();
    
                return response()->json(['message' => 'Data saved successfully.'], 201);
            } else {
                return response()->json(['message' => 'Failed to save data.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while saving pooja details.', 'error' => $e->getMessage()], 500);
        }
    }
    public function getSinglePoojadetails($id)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;
            
            // Fetch the pooja details based on the provided id
            $pooja = Poojadetails::findOrFail($id);
    
            // Fetch the related Poojadetails items along with the Profile
            $panditPujas = Poojadetails::where('status', 'active')
                ->where('id', $pooja->id)
                ->where('pandit_id', $panditId)
                ->get();
    
            // Modify pooja_photo and pooja_video format
            if ($pooja->pooja_photo) {
                $pooja->pooja_photo = [
                    'name' => basename($pooja->pooja_photo),
                ];
            }
    
            if ($pooja->pooja_video) {
                $pooja->pooja_video = [
                    'name' => basename($pooja->pooja_video),
                ];
            }
    
            return response()->json([
                'status' => 200,
                'message' => 'Pooja details fetched successfully',
                'data' => $pooja,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function updatePoojadetails(Request $request, $id)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
            // Find the existing pooja details
            $poojaDetails = Poojadetails::findOrFail($id);
    
            // Ensure the authenticated pandit has permission to update the pooja details
            if ($poojaDetails->pandit_id !== $panditId) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized to update these pooja details.',
                ], 403);
            }
    
            // Handle file uploads
            if ($request->hasFile('pooja_photo')) {
                $image = $request->file('pooja_photo');
                $imagePath = 'uploads/pooja_photo/' . time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/pooja_photo'), $imagePath);
                $poojaDetails->pooja_photo = $imagePath;
            }
    
            if ($request->hasFile('pooja_video')) {
                $video = $request->file('pooja_video');
                $videoPath = 'uploads/pooja_video/' . time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('uploads/pooja_video'), $videoPath);
                $poojaDetails->pooja_video = $videoPath;
            }
    
            // Update the pooja details with the provided data
            
            $poojaDetails->pooja_fee = $request->input('pooja_fee');
            $poojaDetails->pooja_duration = $request->input('pooja_duration');
            $poojaDetails->pooja_done = $request->input('pooja_done');
    
            // Save the updated pooja details
            $poojaDetails->save();
    
            return response()->json([
                'status' => 200,
                'message' => 'Pooja details updated successfully',
                'data' => $poojaDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update pooja details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function deletePoojaDetails($pooja_id)
    {
        try {
            $panditId = Auth::guard('sanctum')->user()->pandit_id;

            // Update status to "deleted" in pandit_poojadetails
            Poojadetails::where('pooja_id', $pooja_id)
                ->where('pandit_id', $panditId)
                ->update(['status' => 'deleted']);

            // Update status to "deleted" in pandit_poojaskill
            Poojaskill::where('pooja_id', $pooja_id)
                ->where('pandit_id', $panditId)
                ->update(['status' => 'deleted']);

            // Update status to "deleted" in pandit_poojaitem
            Poojaitems::where('pooja_id', $pooja_id)
                ->where('pandit_id', $panditId)
                ->update(['status' => 'deleted']);

            return response()->json([
                'status' => 200,
                'message' => 'Pooja details deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete pooja details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
