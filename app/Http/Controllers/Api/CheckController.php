<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Make sure to import this
use App\Models\Profile;
use App\Models\Career;


class CheckController extends Controller
{
    public function checkPanditIdPr()
    {
        try {
            // Get the authenticated user's pandit_id
            $pandit_id = Auth::guard('sanctum')->user()->pandit_id;

            // Check if the pandit_id exists in the Profile table
            $exists = Profile::where('pandit_id', $pandit_id)->exists();

            // Return the appropriate response
            if ($exists) {
                return response()->json([
                    'status' => 200,
                    'message' => 'YES'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'NO'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkPanditIdCr()
    {
        try {
            // Get the authenticated user's pandit_id
            $pandit_id = Auth::guard('sanctum')->user()->pandit_id;

            // Check if the pandit_id exists in the Career table
            $exists = Career::where('pandit_id', $pandit_id)->exists();

            // Return the appropriate response
            if ($exists) {
                return response()->json([
                    'status' => 200,
                    'message' => 'YES'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'NO'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}