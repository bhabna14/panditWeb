<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Models\User;
use Twilio\Rest\Client;
use App\Services\NotificationService;
use App\Models\FCMNotification;
use Illuminate\Support\Facades\Log;

class AdminNotificationController extends Controller
{
    public function create()
    {
        $notifications = FCMNotification::orderBy('created_at', 'desc')->get();
        $platforms = ['android', 'ios', 'web'];
        $users = User::orderBy('name')->select('userid','name','mobile_number','email')->get();

        return view('admin.fcm-notification.send-notification', compact('notifications','platforms','users'));
    }

    public function whatsappcreate()
    {
        $users = User::orderBy('name')->select('userid','name','mobile_number','email')->get();
        return view('admin.fcm-notification.send-whatsaap-notification', compact('users'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image'       => 'nullable|image|max:2048',
            // Audience controls
            'audience'    => 'required|in:all,users,platform',
            'users'       => 'nullable|array',
            'users.*'     => 'nullable|integer',
            'platform'    => 'nullable|array',
            'platform.*'  => 'nullable|string|in:android,ios,web',
            'dry_run'     => 'nullable|boolean',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        // Create row first (queued)
        $notification = FCMNotification::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'image'         => $imagePath,
            'status'        => 'queued',
            'success_count' => 0,
            'failure_count' => 0,
        ]);

        // Build token query
        $tokensQuery = UserDevice::query()
            ->authorized()
            ->whereNotNull('device_id');

        if ($validated['audience'] === 'users' && !empty($validated['users'])) {
            $tokensQuery->whereIn('user_id', $validated['users']);
        }

        if ($validated['audience'] === 'platform' && !empty($validated['platform'])) {
            $tokensQuery->platformIn($validated['platform']);
        }

        $deviceTokens = $tokensQuery->distinct()->pluck('device_id')->toArray();

        if (empty($deviceTokens)) {
            Log::warning('No device tokens found for audience selection.');
            $notification->update(['status' => 'failed']);
            return back()->with('error', 'No valid device tokens found for the selected audience.');
        }

        // Send via FCM
        try {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $notificationService->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/' . $notification->image) : '']
            );

            // Update status using response counts (if available)
            $success = method_exists($resp, 'successes') ? count($resp->successes()->getItems()) : null;
            $failure = method_exists($resp, 'failures') ? count($resp->failures()->getItems()) : null;

            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'App notification queued to FCM successfully!');
        } catch (\Throwable $e) {
            Log::error('FCM send error: '.$e->getMessage());
            $notification->update(['status' => 'failed']);
            return back()->with('error', 'Failed to send notification. '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        FCMNotification::findOrFail($id)->delete();
        return redirect()->route('admin.notification.create')->with('success', 'Notification deleted successfully!');
    }

    public function resend($id)
    {
        try {
            $notification = FCMNotification::findOrFail($id);

            $deviceTokens = UserDevice::authorized()
                ->whereNotNull('device_id')
                ->distinct()
                ->pluck('device_id')
                ->toArray();

            if (empty($deviceTokens)) {
                Log::warning('No valid device tokens found for resending.', ['notification_id' => $id]);
                return back()->with('error', 'No valid device tokens found. Notification could not be resent.');
            }

            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $notificationService->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/' . $notification->image) : '']
            );

            $success = method_exists($resp, 'successes') ? count($resp->successes()->getItems()) : null;
            $failure = method_exists($resp, 'failures') ? count($resp->failures()->getItems()) : null;

            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'Notification resent successfully!');
        } catch (\Exception $e) {
            Log::error('Error resending notification: ' . $e->getMessage(), ['notification_id' => $id]);
            return back()->with('error', 'Failed to resend notification. Please try again later.');
        }
    }

    public function sendWhatsappNotification(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'user'        => 'required|array|min:1',
            'user.*'      => 'integer',
            'description' => 'required|string',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'default_cc'  => 'nullable|string', // e.g. +91
        ]);

        $title       = $validated['title'];
        $description = $validated['description'];
        $userIds     = $validated['user'];
        $imagePath   = $request->file('image') ? $request->file('image')->store('uploads', 'public') : null;

        $users = User::whereIn('userid', $userIds)->get();
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $twilio = new Client($sid, $token);

        $mediaArr = $imagePath ? [asset('storage/'.$imagePath)] : null;
        $body = "*{$title}*\n\n{$description}";

        $failed = [];
        foreach ($users as $user) {
            $to = $this->formatWhatsapp($user->mobile_number, $request->input('default_cc', '+91'));
            try {
                $twilio->messages->create(
                    'whatsapp:' . $to,
                    [
                        'from'     => config('services.twilio.whatsapp_number'), // 'whatsapp:+1415xxxxxxx'
                        'body'     => $body,
                        'mediaUrl' => $mediaArr, // MUST be array or null
                    ]
                );
            } catch (\Throwable $e) {
                Log::error('Twilio WhatsApp send error', ['to' => $to, 'error' => $e->getMessage()]);
                $failed[] = $to;
                continue;
            }
        }

        if (count($failed)) {
            return back()->with('error', 'Some messages failed: ' . implode(', ', $failed));
        }

        return back()->with('success', 'WhatsApp notifications sent successfully!');
    }

    private function formatWhatsapp(?string $raw, string $defaultCc = '+91'): string
    {
        $digits = preg_replace('/\D+/', '', (string)$raw);

        // Add default CC if we see 10-digit local number (India example)
        if (strlen($digits) === 10) {
            return $defaultCc . $digits;
        }

        // If already has CC, just prefix with '+'
        if ($digits && $raw && str_starts_with($raw, '+')) {
            return '+' . $digits;
        }

        // Fallback: try to ensure leading '+'
        return '+' . $digits;
    }
}
