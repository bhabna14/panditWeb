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
    // Strict, conditional validation based on selected audience
    $validated = $request->validate([
        'title'        => 'required|string|max:255',
        'description'  => 'required|string|max:1000',
        'image'        => 'nullable|image|max:2048',
        'audience'     => 'required|in:all,users,platform',

        // When targeting users, users[] must be present & integer IDs
        'users'        => 'required_if:audience,users|array|min:1',
        'users.*'      => 'required_if:audience,users|integer',

        // When targeting platform(s), platform[] must be present & valid
        'platform'     => 'required_if:audience,platform|array|min:1',
        'platform.*'   => 'required_if:audience,platform|in:android,ios,web',

        'dry_run'      => 'nullable|boolean',
    ]);

    $imagePath = $request->hasFile('image')
        ? $request->file('image')->store('notifications', 'public')
        : null;

    // Create DB row (queued)
    $notification = FCMNotification::create([
        'title'         => $validated['title'],
        'description'   => $validated['description'],
        'image'         => $imagePath,
        'status'        => 'queued',
        'success_count' => 0,
        'failure_count' => 0,
    ]);

    // Base query
    $tokensQuery = UserDevice::query()
        ->authorized()
        ->whereNotNull('device_id');

    // Apply strict audience filters
    if ($validated['audience'] === 'users') {
        // We know users[] exists and is all integers due to validation above
        $userIds = array_map('intval', $validated['users']);
        $tokensQuery->whereIn('user_id', $userIds);
    } elseif ($validated['audience'] === 'platform') {
        $tokensQuery->whereIn('platform', $validated['platform']);
    } // audience === 'all' => no extra filters

    // Collect tokens
    $deviceTokens = $tokensQuery->distinct()->pluck('device_id')->toArray();

    if (empty($deviceTokens)) {
        \Log::warning('No device tokens found for the selected audience.', [
            'audience' => $validated['audience'],
            'users'    => $validated['users'] ?? null,
            'platform' => $validated['platform'] ?? null,
        ]);
        $notification->update(['status' => 'failed']);
        return back()->with('error', 'No valid device tokens found for the selected audience.');
    }

    // Send to FCM
    try {
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

        return back()->with('success', 'App notification sent to the selected audience successfully!');
    } catch (\Throwable $e) {
        \Log::error('FCM send error: '.$e->getMessage());
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
        // WhatsApp page still accepts phone numbers directly:
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'user'        => 'required|array|min:1',
            'user.*'      => 'required|string',
            'description' => 'required|string',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'default_cc'  => 'nullable|string',
        ]);

        $title       = $validated['title'];
        $description = $validated['description'];
        $numbers     = $validated['user'];
        $imagePath   = $request->file('image') ? $request->file('image')->store('uploads', 'public') : null;

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $twilio = new Client($sid, $token);

        $mediaArr = $imagePath ? [asset('storage/'.$imagePath)] : null;
        $body = "*{$title}*\n\n{$description}";

        $failed = [];
        foreach ($numbers as $rawNumber) {
            $to = $this->formatWhatsapp($rawNumber, $request->input('default_cc', '+91'));
            try {
                $twilio->messages->create(
                    'whatsapp:' . $to,
                    [
                        'from'     => config('services.twilio.whatsapp_number'),
                        'body'     => $body,
                        'mediaUrl' => $mediaArr,
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

        if (strlen($digits) === 10) {
            return $defaultCc . $digits;
        }
        if ($digits && $raw && str_starts_with($raw, '+')) {
            return '+' . $digits;
        }
        return '+' . $digits;
    }
}
