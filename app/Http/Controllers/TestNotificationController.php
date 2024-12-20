<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\UserDevice;

use Illuminate\Support\Facades\Log;


class TestNotificationController extends Controller
{
    //
   
    
    public function postPodcast(Request $request)
    {
        $podcastTitle = $request->input('title');
        $podcastBody = $request->input('body');
    
        // Retrieve all device tokens
        $deviceTokens = UserDevice::pluck('device_id')->toArray();
    
        if (empty($deviceTokens)) {
            Log::info('No device tokens found for sending notifications.');
            return response()->json(['message' => 'No users to notify.'], 400);
        }
    
        // Use admin credentials path if needed
        try {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
    
            // Send notifications
            $response = $notificationService->sendBulkNotifications(
                $deviceTokens,
                "New Podcast Posted!",
                $podcastTitle,
                ['body' => $podcastBody]
            );
    
            // Log success
            Log::info('Notifications sent successfully.', [
                'podcastTitle' => $podcastTitle,
                'deviceTokens' => $deviceTokens,
                'response' => $response,
            ]);
    
            return response()->json(['message' => 'Notification sent successfully.']);
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error sending notifications.', [
                'error' => $e->getMessage(),
            ]);
    
            return response()->json(['message' => 'Failed to send notifications.', 'error' => $e->getMessage()], 500);
        }
    }
    

}
