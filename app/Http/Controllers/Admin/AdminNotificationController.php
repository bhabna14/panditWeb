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

}
