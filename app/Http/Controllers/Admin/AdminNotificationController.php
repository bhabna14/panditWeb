<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Services\NotificationService;
use App\Models\FCMNotification;
use App\Models\UserUnauthorisedDevices;
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade


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
    
        // Save notification to the database
        $notification = new FCMNotification();
        $notification->title = $request->title;
        $notification->description = $request->description;
        if ($request->hasFile('image')) {
            $notification->image = $request->file('image')->store('notifications', 'public');
        }
        $notification->save();
    
        // Retrieve device tokens, excluding null values
        $deviceTokens = UserDevice::whereNotNull('device_id')->pluck('device_id')->filter()->toArray();
    
        if (!empty($deviceTokens)) {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $notificationService->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image]
            );
        } else {
            \Log::warning('No valid device tokens found.');
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
        try {
            // Find the notification by ID
            $notification = FCMNotification::findOrFail($id);
    
            // Retrieve device tokens, ensuring no null values are included
            $deviceTokens = UserDevice::whereNotNull('device_id')->pluck('device_id')->toArray();
    
            // Check if there are any valid device tokens
            if (!empty($deviceTokens)) {
                $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications(
                    $deviceTokens,
                    $notification->title,
                    $notification->description,
                    ['image' => $notification->image]
                );
    
                Log::info('Notification resent successfully.', [
                    'notification_id' => $id,
                    'device_tokens' => $deviceTokens,
                ]);
    
                return redirect()->back()->with('success', 'Notification resent successfully!');
            } else {
                Log::warning('No valid device tokens found for resending notification.', ['notification_id' => $id]);
                return redirect()->back()->with('error', 'No valid device tokens found. Notification could not be resent.');
            }
        } catch (\Exception $e) {
            Log::error('Error resending notification: ' . $e->getMessage(), [
                'notification_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to resend notification. Please try again later.');
        }
    }
    
    
}
