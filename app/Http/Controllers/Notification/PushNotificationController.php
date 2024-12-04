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
            // Save device information
            UserUnauthorisedDevices::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'device_model' => $request->device_model,
                    'platform' => $request->platform,
                ]
            );
    
            // Prepare notification data
            $data = [
                'registration_ids' => [$request->device_id],
                'notification' => [
                    'title' => 'Welcome to Podcast!',
                    'body' => 'Thank you for downloading our app. Stay tuned for amazing content!',
                    'sound' => 'default',
                ],
            ];
    
            // Send notification
            $response = Http::withHeaders([
                'Authorization' => 'key=BCt0rMrAC1XfdBusFK51rf3UqSN28GjE7xTdOn4n1MGD1AABfz3KkjjJoLN6lZXILwu47XlpLxNklpUm6cqPWUI',
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $data);
            
            // Log the FCM response for debugging
            Log::info('FCM Response Status:', ['status' => $response->status()]);
            Log::info('FCM Response Body:', ['body' => $response->body()]);
            
    
            return response()->json(['message' => 'Device token saved successfully and welcome notification sent.'], 200);
        } catch (\Exception $e) {
            Log::error('Error saving device token or sending notification: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Failed to save device token. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
}
