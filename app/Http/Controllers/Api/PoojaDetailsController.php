<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Poojaskill;
use App\Models\Poojadetails;
use App\Models\Poojalist;
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
    public function getSinglePoojadetails($pooja_id)
    {
        try {
            // Fetch the pooja details based on the provided pooja_id
            $pooja = Poojalist::findOrFail($pooja_id);

            // Fetch the related Poojadetails items along with the Profile
            $panditPujas = Poojadetails::with('profile')
                ->where('status', 'active')
                ->where('pooja_id', $pooja->id)
                ->get();

            // Filter out pandit pujas with a null profile
            $filteredPanditPujas = $panditPujas->filter(function ($poojaDetail) {
                return !is_null($poojaDetail->profile);
            });

            // Modify the photo and video URLs for each pooja
            $pooja->pooja_photo = url('assets/img/' . $pooja->pooja_photo);
            foreach ($filteredPanditPujas as $poojaDetail) {
                $poojaDetail->pooja_photo = url($poojaDetail->pooja_photo);
                $poojaDetail->pooja_video = url($poojaDetail->pooja_video);
                if ($poojaDetail->profile) {
                    $poojaDetail->profile->profile_photo = url($poojaDetail->profile->profile_photo);
                }
            }

            // Prepare the data to return
            $data = [
                'pooja' => $pooja,
                'pandit_pujas' => $filteredPanditPujas->values(), // Convert the collection back to an array
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Pooja details fetched successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch pooja details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    
}
