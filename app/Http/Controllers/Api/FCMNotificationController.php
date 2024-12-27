<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FCMNotification;
use Illuminate\Http\Request;

class FCMNotificationController extends Controller
{
    public function getAllNotifications()
    {
        try {
            // Fetch notifications ordered by the latest created_at
            $notifications = FCMNotification::orderBy('created_at', 'desc')->get();

            // Return success response with data
            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully!',
                'data' => $notifications
            ], 200);
        } catch (\Exception $e) {
            // Log error and return failure response
            \Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}