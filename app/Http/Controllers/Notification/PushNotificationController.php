<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserUnauthorisedDevices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
            UserUnauthorisedDevices::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'device_model' => $request->device_model,
                    'platform' => $request->platform,
                ]
            );

             // Send welcome notification after saving the token
        $data = [
            'registration_ids' => [$request->device_id],
            'notification' => [
                'title' => 'Welcome to Podcast!',
                'body' => 'Thank you for downloading our app. Stay tuned for amazing content!',
                'sound' => 'default',
            ],
        ];

        $response = Http::withToken(config('app.fcm_server_key'))
            ->post('https://fcm.googleapis.com/fcm/send', $data);

    
            return response()->json([  'message' => 'Device token saved successfully and welcome notification sent.',], 200);
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
