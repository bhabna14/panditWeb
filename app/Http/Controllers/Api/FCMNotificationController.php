<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


use App\Models\FCMNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FCMNotificationController extends Controller
{
    public function getAllNotifications()
    {
        try {
            // Fetch notifications ordered by the latest created_at
            $notifications = FCMNotification::orderBy('created_at', 'desc')->get();

            // Map notifications to include accessible image URLs
            $data = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'description' => $notification->description,
                    'image' => $notification->image ? asset('storage/' . $notification->image) : null,
                    'created_at' => $notification->created_at->toDateTimeString(),
                    'updated_at' => $notification->updated_at->toDateTimeString(),
                ];
            });

            // Return success response with mapped data
            return response()->json([
                'status' => 200,
                'message' => 'Notifications retrieved successfully!',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            // Log error and return failure response
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve notifications. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
