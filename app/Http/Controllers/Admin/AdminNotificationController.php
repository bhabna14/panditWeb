<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Services\NotificationService;
class AdminNotificationController extends Controller
{
    //
    public function create()
    {
        return view('admin.fcm-notification.send-notification');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        // Get all device tokens
        $deviceTokens = UserDevice::pluck('device_id')->toArray();

        if (empty($deviceTokens)) {
            return redirect()->back()->with('error', 'No device tokens found.');
        }

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('notifications', 'public');
        }

        // Send notification
        $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
        $notificationService->sendBulkNotifications(
            $deviceTokens,
            $request->input('title'),
            $request->input('description'),
            ['image' => $imageUrl]
        );

        return redirect()->back()->with('success', 'Notification sent successfully!');
    }
}
