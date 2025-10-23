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
use App\Services\Msg91WhatsappService;

class AdminNotificationController extends Controller
{
     public function create(Request $request)
    {
        $notifications = FCMNotification::orderBy('created_at', 'desc')->get();

        // map of userid => display name (fallback to userid if name null)
        $userIndex = User::query()
            ->select('userid','name')
            ->get()
            ->pluck('name','userid');

        $platforms     = ['android', 'ios', 'web'];
        $users         = User::orderBy('name')->select('userid','name','mobile_number','email')->get();
        $prefillUserId = $request->query('user'); // e.g., "USER30382"

        return view('admin.fcm-notification.send-notification', compact(
            'notifications', 'platforms', 'users', 'prefillUserId', 'userIndex'
        ));
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
            'audience'    => 'required|in:all,users,platform',

            'users'       => 'required_if:audience,users|array|min:1',
            'users.*'     => 'required_if:audience,users|string',

            'platform'    => 'required_if:audience,platform|array|min:1',
            'platform.*'  => 'required_if:audience,platform|in:android,ios,web',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        $audience  = $validated['audience'];
        $userIds   = null;
        $platforms = null;

        if ($audience === 'all') {
            $userIds = ['ALL']; // snapshot “ALL”
        } elseif ($audience === 'users') {
            $userIds = collect($validated['users'])
                ->map(fn($v) => is_string($v) ? trim($v) : (string)$v)
                ->filter(fn($v) => $v !== '')
                ->values()->all();

            if (empty($userIds)) {
                return back()->withErrors(['users' => 'Please select at least one valid user.']);
            }
        } else { // platform
            $platforms = array_values(array_unique($validated['platform']));
        }

        // persist snapshot
        $notification = FCMNotification::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'image'         => $imagePath,
            'audience'      => $audience,
            'user_ids'      => $userIds,
            'platforms'     => $platforms,
            'status'        => 'queued',
            'success_count' => 0,
            'failure_count' => 0,
        ]);

        // build device tokens query
        $tokensQuery = UserDevice::query()
            ->authorized()
            ->whereNotNull('device_id');

        if ($audience === 'users') {
            $tokensQuery->whereIn('user_id', $userIds); // user_id holds "USERxxxxx"
        } elseif ($audience === 'platform') {
            $tokensQuery->whereIn('platform', $platforms);
        }

        $deviceTokens = $tokensQuery->distinct()->pluck('device_id')->toArray();

        if (empty($deviceTokens)) {
            \Log::warning('No device tokens found for the selected audience.', [
                'audience' => $audience, 'users' => $userIds, 'platform' => $platforms,
            ]);
            $notification->update(['status' => 'failed']);
            return back()->with('error', 'No valid device tokens found for the selected audience.');
        }

        try {
            $service = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $service->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/'.$notification->image) : '']
            );

            $success = method_exists($resp, 'successes') ? count($resp->successes()->getItems()) : 0;
            $failure = method_exists($resp, 'failures') ? count($resp->failures()->getItems()) : 0;

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
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'user'        => 'required|array|min:1',
            'user.*'      => 'required|string',
            'description' => 'required|string',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'default_cc'  => 'nullable|string', // e.g. +91
        ]);

        $title       = trim($validated['title']);
        $description = trim($validated['description']);
        $numbers     = array_unique(array_map('trim', $validated['user'] ?? []));

        $imagePath   = $request->file('image') ? $request->file('image')->store('uploads', 'public') : null;
        $mediaUrl    = $imagePath ? asset('storage/'.$imagePath) : null;

        $wa = new Msg91WhatsappService();

        $failed   = [];
        $reasons  = []; // collect reason per number for better feedback
        $ok       = [];

        // build body params as per your template: we’ll send [title, description]
        // the service will trim/pad to the configured MSG91_WA_BODY_PARAM_COUNT
        $bodyParams = [$title, $description];

        foreach ($numbers as $rawNumber) {
            $to = $this->formatWhatsapp($rawNumber, $request->input('default_cc', '+91'));

            // Basic sanity: at least 10 digits after normalization
            $digits = preg_replace('/\D+/', '', $to);
            if (strlen($digits) < 10) {
                $failed[]        = $to;
                $reasons[$to]    = 'invalid_number';
                continue;
            }

            try {
                $res    = $wa->sendTemplate($to, $bodyParams, $mediaUrl);
                $status = $res->getStatusCode();
                $body   = (string) $res->getBody();

                // Try to decode response (MSG91 returns JSON)
                $json = null;
                try { $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) {}

                // Decide success based on HTTP and, if present, JSON flags
                $okHttp = ($status >= 200 && $status < 300);

                // Many MSG91 responses include keys like "type":"success" or "success":true
                $okJson = $json && (
                    (isset($json['type']) && strtolower((string) $json['type']) === 'success') ||
                    (isset($json['success']) && $json['success'] === true) ||
                    (isset($json['status']) && in_array(strtolower((string) $json['status']), ['success','queued','accepted'], true))
                );

                if ($okHttp && (!$json || $okJson)) {
                    $ok[] = $to;
                } else {
                    \Log::error('MSG91 WA failed', ['to' => $to, 'status' => $status, 'resp' => $json ?: $body]);
                    $failed[]     = $to;
                    $reasons[$to] = $json['message'] ?? $json['error'] ?? 'send_failed';
                }
            } catch (\Throwable $e) {
                \Log::error('MSG91 WA exception', ['to' => $to, 'error' => $e->getMessage()]);
                $failed[]     = $to;
                $reasons[$to] = $e->getMessage();
            }
        }

        if ($failed) {
            // Show reasons inline to help you debug quickly
            $msg = 'Some messages failed: ' . implode(', ', array_map(function ($n) use ($reasons) {
                return $n . (isset($reasons[$n]) ? ' ('.$reasons[$n].')' : '');
            }, $failed));
            return back()->with('error', $msg);
        }

        return back()->with('success', 'WhatsApp notifications sent successfully!');
    }

    private function formatWhatsapp(?string $raw, string $defaultCc = '+91'): string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);

        // if already starts with +CC
        if (str_starts_with($raw, '+') && strlen($digits) >= 11) {
            return '+' . $digits;
        }
        // local 10-digit (India)
        if (strlen($digits) === 10) {
            return $defaultCc . $digits;
        }
        // common India prefixes (e.g. 0XXXXXXXXXX)
        if (strlen($digits) === 11 && $digits[0] === '0') {
            return $defaultCc . substr($digits, 1);
        }
        // fallback: add plus
        return '+' . $digits;
    }

}
