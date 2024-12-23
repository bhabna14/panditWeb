<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Services\NotificationService;
use App\Models\FCMNotification;
use App\Models\UserUnauthorisedDevices;

class AdminNotificationController extends Controller
{
    //
    public function create()
    {
        $notifications = FCMNotification::orderBy('created_at', 'desc')->get();
        return view('admin.fcm-notification.send-notification', compact('notifications'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);
    
        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('notifications', 'public');
        }
    
        // Save notification to database
        $notification = \App\Models\FCMNotification::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $imageUrl,
            'status' => 'sent',
        ]);
    
        // Get all device tokens
        $deviceTokens = UserDevice::pluck('device_id')->toArray();
    
        if (!empty($deviceTokens)) {
            // Send notification
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $notificationService->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image]
            );
        }
    
        return redirect()->back()->with('success', 'Notification sent successfully!');
    }
    public function delete($id)
    {
        $notification = FCMNotification::findOrFail($id);
        $notification->delete();
    
        return redirect()->route('admin.notification.create')->with('success', 'Notification deleted successfully!');
    }
    public function resend($id)
    {
        $notification = FCMNotification::findOrFail($id);
    
        // Assuming you have a list of recipients to resend the notification to
        $recipients = UserUnauthorisedDevices::pluck('device_id')->toArray();
    
        NotificationService::sendBulkNotifications(
            $notification->title,
            $notification->description,
            ['image' => $notification->image],
            $recipients
        );
    
        return redirect()->back()->with('success', 'Notification resent successfully!');
    }
    
}
