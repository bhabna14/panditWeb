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
        // Validate incoming request
        $request->validate([
            'device_id' => 'required',  // This is the 'device_token'
        ]);
    
        try {
            // Log the incoming request for debugging
            \Log::info('Received saveToken request:', $request->all());
    
            // Save or update the device token in the database
            $device = UserUnauthorisedDevices::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'device_model' => $request->device_model,
                    'platform' => $request->platform,
                ]
            );
    
            // Log the saved device details for verification
            \Log::info('Device token saved:', ['device_id' => $request->device_id, 'device_model' => $request->device_model, 'platform' => $request->platform]);
    
            // Prepare the notification data
            $data = [
                'registration_ids' => [$request->device_id], // Using device_token as device_id
                'notification' => [
                    'title' => 'Welcome to Podcast!',
                    'body' => 'Thank you for downloading our app. Stay tuned for amazing content!',
                    'sound' => 'default',
                ],
            ];
    
            // Log the notification data being sent to FCM
            \Log::info('Notification data being sent to FCM:', $data);
    
            // Send the notification via FCM
            $response = Http::withToken(config('app.fcm_server_key'))
                ->post('https://fcm.googleapis.com/fcm/send', $data);
    
            // Log the response from FCM for debugging
            \Log::info('FCM response:', $response->json());
    
            // Check if the response from FCM is successful
            if ($response->successful()) {
                return response()->json([
                    'message' => 'Device token saved successfully and welcome notification sent.',
                ], 200);
            } else {
                // Log any error response from FCM
                \Log::error('Error from FCM:', ['response' => $response->json()]);
    
                return response()->json([
                    'message' => 'Device token saved, but failed to send notification.',
                    'error' => $response->json(),
                ], 500);
            }
    
        } catch (\Exception $e) {
            // Log any exceptions that occur during execution
            \Log::error('Error in saveToken method:', ['error' => $e->getMessage()]);
    
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'Failed to save device token. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
