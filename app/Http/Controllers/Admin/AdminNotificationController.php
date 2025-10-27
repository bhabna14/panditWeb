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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    public function whatsappcreate(Request $request)
    {
        // Pull a concise list for the selector (only those having a number)
        $users = User::query()
            ->select('id', 'name', 'email', 'mobile_number')
            ->whereNotNull('mobile_number')
            ->where('mobile_number', '!=', '')
            ->orderBy('name')
            ->limit(1000) // safety cap
            ->get();

        return view('admin.fcm-notification.send-whatsaap-notification', compact('users'));
    }

    public function whatsappSend(Request $request)
    {
        $validated = $request->validate([
            'audience'     => ['required', Rule::in(['all','selected'])],
            'user'         => ['nullable','array'],
            'user.*'       => ['nullable','string'],
            'default_cc'   => ['nullable','string'], // e.g. +91
            'title'        => ['required','string','max:255'],
            'description'  => ['required','string'],
            'image'        => ['nullable','file','mimes:jpg,jpeg,png,webp|max:4096'],
        ]);

        $defaultCc   = $validated['default_cc'] ?? '+91';
        $title       = trim($validated['title']);
        $description = trim($validated['description']);

        // Resolve recipients
        if ($validated['audience'] === 'all') {
            $rawNumbers = User::query()
                ->whereNotNull('mobile_number')
                ->where('mobile_number','!=','')
                ->pluck('mobile_number')
                ->all();
        } else {
            // Selected (and free-typed) numbers coming from the Select2
            $rawNumbers = array_unique(array_map('trim', $validated['user'] ?? []));
        }

        // Normalize phone numbers
        $recipients = [];
        foreach ($rawNumbers as $raw) {
            $to = $this->formatWhatsapp($raw, $defaultCc);
            $digits = preg_replace('/\D+/', '', $to);
            if (strlen($digits) >= 10) {
                $recipients[] = $to;
            }
        }
        $recipients = array_values(array_unique($recipients));

        if (empty($recipients)) {
            return back()->with('error', 'No valid phone numbers found to send.')->withInput();
        }

        // Optional image
        $mediaUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/whatsapp', 'public');
            $mediaUrl = Storage::disk('public')->url($path);
        }

        // Build template body params (title first → bolded at client, plus description)
        $bodyParams = [$title, $description];

        $wa = app(Msg91WhatsappService::class);

        $ok = [];
        $failed = [];
        $reasons = [];

        foreach ($recipients as $to) {
            try {
                $resp = $wa->sendTemplate($to, $bodyParams, $mediaUrl);
                $status = $resp['http_status'] ?? 0;
                $json   = $resp['json'] ?? null;
                $okHttp = ($status >= 200 && $status < 300);

                $okJson = $json && (
                    (isset($json['type'])   && strtolower((string)$json['type'])   === 'success') ||
                    (isset($json['success']) && $json['success'] === true) ||
                    (isset($json['status']) && in_array(strtolower((string)$json['status']), ['success','queued','accepted'], true))
                );

                if ($okHttp && (!$json || $okJson)) {
                    $ok[] = $to;
                } else {
                    Log::error('MSG91 WA failed', ['to' => $to, 'resp' => $resp]);
                    $failed[] = $to;
                    $reasons[$to] = $json['message'] ?? $json['error'] ?? 'send_failed';
                }
            } catch (\Throwable $e) {
                Log::error('MSG91 WA exception', ['to' => $to, 'error' => $e->getMessage()]);
                $failed[] = $to;
                $reasons[$to] = $e->getMessage();
            }
        }

        if ($failed) {
            $msg = 'Some messages failed: ' . implode(', ', array_map(
                fn($n) => $n . (isset($reasons[$n]) ? ' ('.$reasons[$n].')' : ''),
                $failed
            ));
            return back()->with('error', $msg);
        }

        return back()->with('success', 'WhatsApp notifications sent successfully to '.count($ok).' recipient(s).');
    }

    private function formatWhatsapp(?string $raw, string $defaultCc = '+91'): string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);

        if (str_starts_with($raw, '+') && strlen($digits) >= 11) {
            return '+' . $digits;
        }
        if (strlen($digits) === 10) {
            return $defaultCc . $digits;
        }
        if (strlen($digits) === 11 && $digits[0] === '0') {
            return $defaultCc . substr($digits, 1);
        }
        return '+' . $digits;
    }

}
