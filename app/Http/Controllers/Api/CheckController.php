<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import this for authentication
use App\Models\Profile;
use App\Models\Career;

class CheckController extends Controller
{
    public function checkPanditProfile()
    {
        try {
            // Get the authenticated user's pandit_id
            $pandit_id = Auth::guard('sanctum')->user()->pandit_id;

            // Check if the pandit_id exists in the Profile table
            $profileExists = Profile::where('pandit_id', $pandit_id)->exists();

            // Check if the pandit_id exists in the Career table
            $careerExists = Career::where('pandit_id', $pandit_id)->exists();

            // Determine the response message
            $message = '';

            if ($profileExists) {
                $message = 'Profile exists';
            } elseif ($careerExists) {
                $message = 'Career exists';
            } else {
                $message = 'No record found';
            }

            // Return the appropriate response
            return response()->json([
                'status' => $profileExists || $careerExists ? 200 : 200,
                'message' => $message
            ], $profileExists || $careerExists ? 200 : 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}