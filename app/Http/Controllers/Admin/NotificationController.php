<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function checkNewNotifications()
    {
        // Check for the latest unread order notification
        $new_order = Notification::where('is_read', false)->latest()->first();
    
        if ($new_order) {
            // Mark the notification as read
            $new_order->update(['is_read' => true]);
    
            // Return the response with the new order notification details
            return response()->json([
                'new_order' => true,
                'notification' => [
                    'message' => 'New order received: ' . $new_order->data['message'],
                    'time' => $new_order->created_at->diffForHumans(),
                    'url' => url('orders/' . $new_order->id)  // Example URL
                ]
            ]);
        }
    
        return response()->json(['new_order' => false]);
    }
    
    
}