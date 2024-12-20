<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\UserUnauthorisedDevices;

class TestNotificationController extends Controller
{
    //
    public function postPodcast(Request $request)
{
    $podcastTitle = $request->input('title');
    $podcastBody = $request->input('body');

    // Retrieve all device tokens
    $deviceTokens = UserUnauthorisedDevices::pluck('device_id')->toArray();

    if (empty($deviceTokens)) {
        return response()->json(['message' => 'No users to notify.'], 400);
    }

    // Use admin credentials path if needed
    $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));

    // Send notifications
    $notificationService->sendBulkNotifications(
        $deviceTokens,
        "New Podcast Posted!",
        $podcastTitle,
        ['body' => $podcastBody]
    );

    return response()->json(['message' => 'Notification sent successfully.']);
}

}
