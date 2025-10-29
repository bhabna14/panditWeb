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
        $users = User::query()
            ->select('id','name','email','mobile_number')
            ->whereNotNull('mobile_number')
            ->where('mobile_number','!=','')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        // view expects nothing from env; we’ll pass flags from service
        $requiresParam = Msg91WhatsappService::requiresUrlParam();
        $buttonBase    = Msg91WhatsappService::buttonBase();
        $senderLabel   = Msg91WhatsappService::senderE164();

        return view('admin.fcm-notification.send-whatsaap-notification', compact(
            'users','requiresParam','buttonBase','senderLabel'
        ));
    }

    // ---------------- WHATSAPP SEND ----------------
    public function whatsappSend(Request $request)
    {
        $requiresParam = Msg91WhatsappService::requiresUrlParam();
        $bodyFields    = Msg91WhatsappService::bodyFields();

        $validated = $request->validate([
            'audience'         => ['required', Rule::in(['all','selected'])],
            'user'             => ['nullable','array'],
            'user.*'           => ['nullable','string'],
            'title'            => ['required','string','max:255'],
            'description'      => ['required','string'],
            'button_url_value' => [$requiresParam ? 'required' : 'nullable','string','max:2000'],
        ]);

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
            $rawNumbers = array_unique(array_map('trim', $validated['user'] ?? []));
        }

        // Normalize -> MSISDN (digits with country code)
        $defaultCcDigits = $this->deriveDefaultCcDigitsFromNumber(Msg91WhatsappService::integratedNumber());
        $toMsisdns = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, $defaultCcDigits);
            if ($msisdn !== null) $toMsisdns[] = $msisdn;
        }
        $toMsisdns = array_values(array_unique($toMsisdns));
        if (!$toMsisdns) {
            return back()->with('error', 'No valid phone numbers found to send.')->withInput();
        }

        // Sanitize (MSG91 bulk forbids \n in body values)
        $titleClean = $this->sanitizeBodyValue($title);
        $descClean  = $this->sanitizeBodyValue($description);

        // Optional shield to avoid WhatsApp “Copy code” UI (kept enabled for you)
        $titleClean = $this->shieldDigits($titleClean);
        $descClean  = $this->shieldDigits($descClean);

        // Build components strictly as per your template
        $components = [];
        if ($bodyFields === 2) {
            $components['body_1'] = ['type' => 'text', 'value' => $titleClean];
            $components['body_2'] = ['type' => 'text', 'value' => $descClean];
        } elseif ($bodyFields === 1) {
            $components['body_1'] = ['type' => 'text', 'value' => $titleClean . ' — ' . $descClean];
        } // if 0, no body_* component

        // URL button {{1}} token (required for your template)
        if ($requiresParam) {
            $rawVal = (string)($validated['button_url_value'] ?? '');
            $param  = $this->normalizeButtonParam($rawVal, Msg91WhatsappService::buttonBase());

            if ($param === '') {
                return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();
            }

            $components['button_1'] = [
                'subtype' => 'url',
                'type'    => 'text',
                'value'   => $param, // ONLY the token for {{1}}
            ];
        }

        /** @var Msg91WhatsappService $wa */
        $wa = app(Msg91WhatsappService::class);

        try {
            $resp   = $wa->sendBulkTemplate($toMsisdns, $components);
            $status = $resp['http_status'] ?? 0;
            $json   = $resp['json'] ?? null;

            $okHttp = ($status >= 200 && $status < 300);
            $okJson = false;
            $reason = null;

            if ($json) {
                $statusField = strtolower((string)($json['status'] ?? $json['type'] ?? ''));
                $okJson = in_array($statusField, ['success','queued','accepted'], true)
                       || ($json['success'] ?? false) === true;

                $reason = $json['message']
                    ?? ($json['error']['message'] ?? null)
                    ?? ($json['errors'][0]['message'] ?? null)
                    ?? ($json['errors'][0] ?? null)
                    ?? null;
            }

            if ($okHttp && (!$json || $okJson)) {
                return back()->with('success', 'WhatsApp notifications queued to '.count($toMsisdns).' recipient(s).');
            }

            \Log::error('MSG91 bulk WA failed', ['http_status'=>$status, 'json'=>$json, 'body'=>$resp['body'] ?? null]);
            return back()->with('error', 'MSG91 bulk send failed: ' . ($reason ?: 'send_failed'));

        } catch (\Throwable $e) {
            \Log::error('MSG91 bulk WA exception', ['error'=>$e->getMessage()]);
            return back()->with('error', 'MSG91 bulk send error: '.$e->getMessage());
        }
    }

    /* ---------- helpers (no env) ---------- */

    private function deriveDefaultCcDigitsFromNumber(string $integratedNumberDigits): string
    {

        if (str_starts_with($integratedNumberDigits, '91')) return '91';
        // Fallback: take up to first 3 digits
        return substr($integratedNumberDigits, 0, 3) ?: '91';
    }

    private function toMsisdn(?string $raw, string $defaultCcDigits): ?string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);

        if ($raw !== '' && $raw[0] === '+' && strlen($digits) >= 11) return $digits;              // +CC######## -> digits
        if (strlen($digits) === 10)                                   return $defaultCcDigits.$digits; // local 10
        if (strlen($digits) === 11 && $digits[0] === '0')             return $defaultCcDigits.substr($digits,1);
        if (strlen($digits) >= 11)                                    return $digits;                 // already with CC
        return null;
    }

    private function sanitizeBodyValue(string $s): string
    {
        $s = str_replace(["\r", "\n"], ' ', $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    /**
     * Break 4–8 digit runs to avoid WhatsApp "Copy code" banner.
     */
    private function shieldDigits(string $s): string
    {
        return preg_replace_callback('/(?<!\d)(\d{4,8})(?!\d)/', function ($m) {
            $digits = $m[1];
            $mid    = intdiv(strlen($digits), 2);
            return substr($digits, 0, $mid) . " " . substr($digits, $mid); // U+2009 thin space
        }, $s);
    }

    /**
     * If your MSG91 button URL is like https://base/@{{1}},
     * we only send the token for {{1}}.
     */
    private function normalizeButtonParam(string $input, string $base): string
    {
        $clean = $this->sanitizeBodyValue($input);
        if ($clean === '') return '';

        if ($base !== '') {
            $baseNorm = rtrim($base, '/') . '/';
            if (stripos($clean, $baseNorm) === 0) {
                $clean = substr($clean, strlen($baseNorm));   // only token for {{1}}
            }
        }

        $clean = ltrim($clean, " /");
        $clean = preg_replace('/\s+/', '-', $clean);
        return trim(str_replace(["\r", "\n"], '', $clean));
    }
}
