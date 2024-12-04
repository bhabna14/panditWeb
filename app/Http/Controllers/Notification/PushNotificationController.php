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
            // Log the incoming request for debugging
            \Log::info('Received saveToken request:', $request->all());
    
            // Save the device details
            UserUnauthorisedDevices::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'device_model' => $request->device_model,
                    'platform' => $request->platform,
                ]
            );
    
            // Log the saved data
            \Log::info('Device token saved:', [
                'device_id' => $request->device_id,
                'device_model' => $request->device_model,
                'platform' => $request->platform,
            ]);
    
            // Prepare the notification data
            $data = [
                'registration_ids' => [$request->device_id],
                'notification' => [
                    'title' => 'Welcome to Podcast!',
                    'body' => 'Thank you for downloading our app. Stay tuned for amazing content!',
                    'sound' => 'default',
                ],
            ];
    
            // Log the notification data being sent
            \Log::info('Notification data being sent to FCM:', $data);
    
            // Send the notification using Firebase Cloud Messaging (FCM)
            $response = Http::withToken(config('app.fcm_server_key'))
                ->post('https://fcm.googleapis.com/fcm/send', $data);
    
            // Log the FCM response
            \Log::info('FCM response:', $response->json());
    
            return response()->json([
                'message' => 'Device token saved successfully and welcome notification sent.',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error saving device token:', ['error' => $e->getMessage()]);
    
            // Return a JSON response with an error message
            return response()->json([
                'message' => 'Failed to save device token. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
}
