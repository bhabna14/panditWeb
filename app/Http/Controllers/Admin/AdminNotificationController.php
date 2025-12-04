<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\Msg91WhatsappService;
use App\Models\FCMNotification;
use App\Models\UserDevice;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage; 
use Twilio\Rest\Client;

class AdminNotificationController extends Controller
{
   public function whatsappcreate(Request $request)
    {
        $users = User::query()
            ->select('id', 'name', 'email', 'mobile_number')
            ->whereNotNull('mobile_number')
            ->where('mobile_number', '!=', '')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        $requiresParam = Msg91WhatsappService::requiresUrlParam();
        $buttonBase    = Msg91WhatsappService::buttonBase();
        $senderLabel   = Msg91WhatsappService::senderE164();
        $templateName  = Msg91WhatsappService::templateName();   // "customer"

        return view('admin.fcm-notification.send-whatsaap-notification', compact(
            'users',
            'requiresParam',
            'buttonBase',
            'senderLabel',
            'templateName'
        ));
    }

    public function whatsappSend(Request $request)
    {
        $validated = $request->validate([
            'audience'    => ['required', Rule::in(['all', 'selected'])],
            'user'        => ['nullable', 'array'],
            'user.*'      => ['nullable', 'string'],
            // These map to header_1 and body_1
            'title'       => ['nullable', 'string', 'max:255'],  // header_1
            'description' => ['nullable', 'string'],             // body_1
        ]);

        // Resolve recipients
        if ($validated['audience'] === 'all') {
            $rawNumbers = User::query()
                ->whereNotNull('mobile_number')
                ->where('mobile_number', '!=', '')
                ->pluck('mobile_number')
                ->all();
        } else {
            $rawNumbers = array_unique(array_map('trim', $validated['user'] ?? []));
        }

        // Normalize → MSISDN
        $defaultCcDigits = $this->deriveDefaultCcDigitsFromNumber(
            Msg91WhatsappService::integratedNumber()
        );

        $toMsisdns = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, $defaultCcDigits);
            if ($msisdn !== null) {
                $toMsisdns[] = $msisdn;
            }
        }

        $toMsisdns = array_values(array_unique($toMsisdns));

        if (!$toMsisdns) {
            return back()
                ->with('error', 'No valid phone numbers found to send.')
                ->withInput();
        }

        // Clean title/description before sending as template params
        $title = $this->sanitizeBodyValue($validated['title'] ?? '');
        $desc  = $this->sanitizeBodyValue($validated['description'] ?? '');

        /** @var Msg91WhatsappService $wa */
        $wa = app(Msg91WhatsappService::class);

        try {
            // title     -> header_1
            // desc      -> body_1
            $resp   = $wa->sendBulkTemplate($toMsisdns, $title, $desc);
            $status = $resp['http_status'] ?? 0;
            $json   = $resp['json'] ?? null;

            $okHttp = ($status >= 200 && $status < 300);
            $okJson = false;
            $reason = null;

            if ($json) {
                $statusField = strtolower((string)($json['status'] ?? $json['type'] ?? ''));
                $okJson = in_array($statusField, ['success', 'queued', 'accepted'], true)
                    || ($json['success'] ?? false) === true;

                $reason = $json['message']
                    ?? ($json['error']['message'] ?? null)
                    ?? ($json['errors'][0]['message'] ?? null)
                    ?? ($json['errors'][0] ?? null)
                    ?? null;
            }

            if ($okHttp && (!$json || $okJson)) {
                return back()->with(
                    'success',
                    'WhatsApp notifications queued to ' . count($toMsisdns) . ' recipient(s).'
                );
            }

            Log::error('MSG91 bulk WA failed', [
                'http_status' => $status,
                'json'        => $json,
                'body'        => $resp['body'] ?? null,
            ]);

            return back()->with(
                'error',
                'MSG91 bulk send failed: ' . ($reason ?: 'send_failed')
            );
        } catch (\Throwable $e) {
            Log::error('MSG91 bulk WA exception', ['error' => $e->getMessage()]);

            return back()->with(
                'error',
                'MSG91 bulk send error: ' . $e->getMessage()
            );
        }
    }

    private function deriveDefaultCcDigitsFromNumber(string $integratedNumberDigits): string
    {
        if (str_starts_with($integratedNumberDigits, '91')) {
            return '91';
        }

        return substr($integratedNumberDigits, 0, 3) ?: '91';
    }

    private function toMsisdn(?string $raw, string $defaultCcDigits): ?string
    {
        $raw    = (string)$raw;
        $digits = preg_replace('/\D+/', '', $raw);

        // Already in +CC######## style
        if ($raw !== '' && $raw[0] === '+' && strlen($digits) >= 11) {
            return $digits;
        }

        // Local 10-digit (India)
        if (strlen($digits) === 10) {
            return $defaultCcDigits . $digits;
        }

        // 0 + 10 digits
        if (strlen($digits) === 11 && $digits[0] === '0') {
            return $defaultCcDigits . substr($digits, 1);
        }

        // Already with CC (11+ digits)
        if (strlen($digits) >= 11) {
            return $digits;
        }

        return null;
    }

    private function sanitizeBodyValue(string $s): string
    {
        $s = str_replace(["\r", "\n"], ' ', $s);

        return trim(preg_replace('/\s+/u', ' ', $s));
    }
    
    public function create(Request $request)
    {
        $notifications = FCMNotification::orderBy('created_at', 'desc')->get();

        // map of userid => display name (fallback to userid if name null)
        $userIndex = User::query()
            ->select('userid', 'name')
            ->get()
            ->pluck('name', 'userid');

        $platforms     = ['android', 'ios', 'web'];
        $users         = User::orderBy('name')
            ->select('userid', 'name', 'mobile_number', 'email')
            ->get();
        $prefillUserId = $request->query('user'); // e.g., "USER30382"

        return view('admin.fcm-notification.send-notification', compact(
            'notifications',
            'platforms',
            'users',
            'prefillUserId',
            'userIndex'
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

        // Snapshot audience
        if ($audience === 'all') {
            $userIds = ['ALL']; // snapshot “ALL” for history
        } elseif ($audience === 'users') {
            $userIds = collect($validated['users'])
                ->map(fn($v) => is_string($v) ? trim($v) : (string) $v)
                ->filter(fn($v) => $v !== '')
                ->values()
                ->all();

            if (empty($userIds)) {
                return back()->withErrors(['users' => 'Please select at least one valid user.']);
            }
        } else { // audience === 'platform'
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

        // build device tokens query (RESPECT audience)
        $tokensQuery = UserDevice::query()
            ->authorized()
            ->whereNotNull('device_id');

        if ($audience === 'users') {
            // IMPORTANT: user_id column must store your string userid (e.g. "USER30382")
            $tokensQuery->whereIn('user_id', $userIds);
        } elseif ($audience === 'platform') {
            $tokensQuery->whereIn('platform', $platforms);
        }
        // audience = all → no extra filter

        $deviceTokens = $tokensQuery->distinct()->pluck('device_id')->toArray();

        if (empty($deviceTokens)) {
            Log::warning('No device tokens found for the selected audience.', [
                'audience' => $audience,
                'users'    => $userIds,
                'platform' => $platforms,
            ]);

            $notification->update(['status' => 'failed']);

            return back()->with('error', 'No valid device tokens found for the selected audience.');
            ;

        }

        try {
            $service = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $service->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/' . $notification->image) : '']
            );

            $success = $resp && method_exists($resp, 'successes')
                ? count($resp->successes()->getItems())
                : 0;
            $failure = $resp && method_exists($resp, 'failures')
                ? count($resp->failures()->getItems())
                : 0;

            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'App notification sent to the selected audience successfully!');
        } catch (\Throwable $e) {
            Log::error('FCM send error: ' . $e->getMessage(), [
                'audience' => $audience,
                'users'    => $userIds,
                'platform' => $platforms,
            ]);

            $notification->update(['status' => 'failed']);

            return back()->with('error', 'Failed to send notification. ' . $e->getMessage());
        }
    }

    public function resend($id)
    {
        try {
            $notification = FCMNotification::findOrFail($id);

            $audience  = $notification->audience ?? 'all';
            $userIds   = $notification->user_ids ?? null;   // cast to array in model
            $platforms = $notification->platforms ?? null;  // cast to array in model

            // If audience === 'all' we ignore user_ids completely (in case it has ["ALL"])
            if ($audience === 'users' && (empty($userIds) || !is_array($userIds))) {
                Log::warning('Resend attempted with users audience, but no user_ids snapshot.', [
                    'notification_id' => $id,
                ]);

                return back()->with(
                    'error',
                    'Original selected users are missing, cannot resend user-wise.'
                );
            }

            if ($audience === 'platform' && (empty($platforms) || !is_array($platforms))) {
                Log::warning('Resend attempted with platform audience, but no platforms snapshot.', [
                    'notification_id' => $id,
                ]);

                return back()->with(
                    'error',
                    'Original platforms are missing, cannot resend platform-wise.'
                );
            }

            // Build device tokens again from ORIGINAL snapshot:
            $deviceTokens = $this->buildDeviceTokensForAudience($audience, $userIds, $platforms);

            if (empty($deviceTokens)) {
                Log::warning('No valid device tokens found for resending.', [
                    'notification_id' => $id,
                    'audience'        => $audience,
                ]);

                return back()->with('error', 'No valid device tokens found. Notification could not be resent.');
            }

            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));

            $resp = $notificationService->sendBulkNotifications(
                $deviceTokens,
                $notification->title,
                $notification->description,
                ['image' => $notification->image ? asset('storage/' . $notification->image) : '']
            );

            $success = $resp && method_exists($resp, 'successes')
                ? count($resp->successes()->getItems())
                : 0;
            $failure = $resp && method_exists($resp, 'failures')
                ? count($resp->failures()->getItems())
                : 0;

            // overwrite with latest resend attempt
            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'Notification resent successfully!');
        } catch (\Throwable $e) {
            Log::error('Error resending notification: ' . $e->getMessage(), [
                'notification_id' => $id,
            ]);

            return back()->with('error', 'Failed to resend notification. Please try again later.');
        }
    }

    protected function buildDeviceTokensForAudience(string $audience, ?array $userIds, ?array $platforms): array
    {
        // Start base query
        $tokensQuery = UserDevice::query()
            ->authorized()
            ->whereNotNull('device_id');

        if ($audience === 'users') {
            // filter out any weird/empty values
            $cleanUserIds = collect($userIds ?? [])
                ->map(fn($v) => is_string($v) ? trim($v) : (string) $v)
                ->filter(fn($v) => $v !== '' && strtoupper($v) !== 'ALL')
                ->values()
                ->all();

            if (!empty($cleanUserIds)) {
                $tokensQuery->whereIn('user_id', $cleanUserIds);
            } else {
                // nothing valid → will return empty
                return [];
            }
        } elseif ($audience === 'platform') {
            $cleanPlatforms = collect($platforms ?? [])
                ->map(fn($v) => strtolower(trim($v)))
                ->filter(fn($v) => in_array($v, ['android', 'ios', 'web'], true))
                ->values()
                ->all();

            if (!empty($cleanPlatforms)) {
                $tokensQuery->whereIn('platform', $cleanPlatforms);
            } else {
                return [];
            }
        }
        // audience === 'all' → no extra filter

        return $tokensQuery->distinct()->pluck('device_id')->toArray();
    }

    public function delete($id)
    {
        FCMNotification::findOrFail($id)->delete();
        return redirect()->route('admin.notification.create')->with('success', 'Notification deleted successfully!');
    }
}
