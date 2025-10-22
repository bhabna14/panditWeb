<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MarketingFollowUp;
use Carbon\Carbon;

// ★ NEW: imports for notifications
use App\Models\UserDevice;
use App\Models\FCMNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class FollowUpController extends Controller
{
    public function followUpSubscriptions()
    {
        // Get orders related to subscriptions ending in the next 5 days
        $orders = Order::whereNull('request_id')
            ->with([
                'subscription' => function ($query) {
                    $query->where('status', 'active')
                        ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(5)]);
                },
                'user',
                'address.localityDetails',
                'flowerProduct',
                // Include followups for the “View Notes” modal
                'marketingFollowUps'
            ])
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active')
                    ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(5)]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.marketing.follow-up-subscriptions', compact('orders'));
    }

    public function saveFollowUp(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'note'     => 'required|string',
        ]);

        MarketingFollowUp::create([
            'order_id'        => $request->order_id,
            'subscription_id' => $request->subscription_id,
            'user_id'         => $request->user_id,
            'followup_date'   => now()->toDateString(),
            'note'            => $request->note,
            'created_at'      => now(),
        ]);

        return back()->with('success', 'Follow-up information saved successfully.');
    }

    /**
     * ★ NEW: Send a push notification (FCM) to a single user (by users.userid).
     */
    public function sendUserNotification(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|string',       // this is users.userid (e.g., USER30382)
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image'       => 'nullable|image|max:4096',
        ]);

        // Store image (optional)
        $imagePath = $request->file('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        // Create an FCMNotification row (for history)
        $notification = FCMNotification::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'image'         => $imagePath,
            'status'        => 'queued',
            'success_count' => 0,
            'failure_count' => 0,
        ]);

        // Collect this user's device tokens. IMPORTANT:
        // UserDevice.user_id stores users.userid (string), not users.id
        $deviceTokens = UserDevice::query()
            ->where('user_id', $validated['user_id'])
            ->whereNotNull('device_id')
            ->distinct()
            ->pluck('device_id')
            ->toArray();

        if (empty($deviceTokens)) {
            Log::warning('No device tokens for user', ['user_id' => $validated['user_id']]);
            $notification->update(['status' => 'failed']);
            return back()->with('danger', 'No valid device tokens found for this user.');
        }

        try {
            $service = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $service->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/' . $notification->image) : '']
            );

            // Try to derive counts (Kreait responses expose successes/failures)
            $success = method_exists($resp, 'successes') ? count($resp->successes()->getItems()) : count($deviceTokens);
            $failure = method_exists($resp, 'failures') ? count($resp->failures()->getItems()) : 0;

            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'Notification sent to the user successfully!');
        } catch (\Throwable $e) {
            Log::error('FCM single-user send error: '.$e->getMessage(), [
                'user_id' => $validated['user_id']
            ]);
            $notification->update(['status' => 'failed']);
            return back()->with('danger', 'Failed to send notification to this user. '.$e->getMessage());
        }
    }
}
