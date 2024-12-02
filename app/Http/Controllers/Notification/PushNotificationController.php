<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserUnauthorisedDevices;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    //
    public function saveToken(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
        ]);
    
        try {
            // Use a transaction if needed for additional safety
            UserDevice::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'device_model' => $request->device_model,
                    'platform' => $request->platform,
                ]
            );
    
            return response()->json(['message' => 'Device token saved successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error saving device token: ' . $e->getMessage());
    
            // Return a JSON response with an error message
            return response()->json([
                'message' => 'Failed to save device token. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
