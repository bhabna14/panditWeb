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

    private const MSG91_AUTHKEY       = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER   = '919124420330'; // digits only (no +)
    private const TEMPLATE_NAME       = 'flower_wp_message';
    private const TEMPLATE_NAMESPACE  = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const LANGUAGE_CODE       = 'en_US';
    private const ENDPOINT_BULK       = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // === Template & send knobs ===
    private const BODY_FIELDS         = 0;      // your template body has 0 params
    private const REQUIRES_URL_PARAM  = true;   // button at index 0 is URL and needs {{1}}
    private const DEFAULT_CC          = '91';   // for 10-digit local numbers

    // Batch/flow control (helps avoid 131049)
    private const BATCH_SIZE          = 200;    // MSG91 bulk supports arrays; keep batches modest
    private const SLEEP_BASE_MS       = 300;    // base sleep between batches
    private const SLEEP_JITTER_MS     = 250;    // +/- jitter

    public function whatsappcreate(Request $request)
    {
        $users = User::query()
            ->select('id','name','email','mobile_number')
            ->whereNotNull('mobile_number')
            ->where('mobile_number','!=','')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        return view('admin.fcm-notification.send-whatsaap-notification', compact('users'));
    }

    public function whatsappSend(Request $request)
    {
        $validated = $request->validate([
            'audience'         => ['required', Rule::in(['all','selected'])],
            'user'             => ['nullable','array'],
            'user.*'           => ['nullable','string'],
            'title'            => ['required','string','max:255'], // collected but not sent to body (BODY_FIELDS=0)
            'description'      => ['required','string'],           // collected but not sent to body (BODY_FIELDS=0)
            'button_url_value' => [self::REQUIRES_URL_PARAM ? 'required' : 'nullable','string','max:2000'],
        ]);

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

        // Normalize to MSISDN and de-dupe
        $toMsisdns = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, self::DEFAULT_CC);
            if ($msisdn) $toMsisdns[$msisdn] = true;
        }
        $toMsisdns = array_keys($toMsisdns);

        if (!$toMsisdns) {
            return back()->with('error', 'No valid phone numbers found to send.')->withInput();
        }

        // Build components exactly as approved
        $components = [];
        // BODY_FIELDS = 0 => do NOT send any body_* components

        // Required URL button {{1}}
        $token = $this->normalizeButtonParam((string)($validated['button_url_value'] ?? ''));
        if (self::REQUIRES_URL_PARAM && $token === '') {
            return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();
        }
        $components['button_1'] = [
            'subtype' => 'url',
            'type'    => 'text',
            'value'   => $token, // only the token for {{1}}
        ];

        // Batch send
        $client  = new HttpClient(['timeout'=>25]);
        $headers = [
            'authkey'      => self::MSG91_AUTHKEY,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $total    = count($toMsisdns);
        $sent     = 0;
        $failures = [];

        foreach (array_chunk($toMsisdns, self::BATCH_SIZE) as $idx => $chunk) {
            $payload = [
                'integrated_number' => self::INTEGRATED_NUMBER,
                'content_type'      => 'template',
                'payload'           => [
                    'messaging_product' => 'whatsapp',
                    'type'              => 'template',
                    'template'          => [
                        'name'      => self::TEMPLATE_NAME,
                        'language'  => [
                            'code'   => self::LANGUAGE_CODE,
                            'policy' => 'deterministic',
                        ],
                        'namespace' => self::TEMPLATE_NAMESPACE,
                        'to_and_components' => [[
                            'to'         => array_map(fn($n) => preg_replace('/\D+/', '', $n), $chunk),
                            'components' => $components,
                        ]],
                    ],
                ],
            ];

            try {
                $res   = $client->post(self::ENDPOINT_BULK, ['headers'=>$headers, 'json'=>$payload]);
                $code  = $res->getStatusCode();
                $body  = (string) $res->getBody();
                $json  = null; try { $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) {}

                $okHttp = ($code >= 200 && $code < 300);
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
                    $sent += count($chunk);
                } else {
                    $failures[] = [
                        'batch'  => $idx,
                        'http'   => $code,
                        'reason' => $reason ?: 'send_failed',
                        'resp'   => $json ?? $body,
                    ];
                    Log::warning('MSG91 batch failed', end($failures));
                }
            } catch (\Throwable $e) {
                $failures[] = ['batch'=>$idx, 'http'=>0, 'reason'=>$e->getMessage()];
                Log::error('MSG91 bulk WA exception', ['batch'=>$idx, 'error'=>$e->getMessage()]);
            }

            // small randomized gap between batches to reduce “ecosystem engagement” blocks
            if ($idx < ceil($total/self::BATCH_SIZE)-1) {
                $sleepMs = self::SLEEP_BASE_MS + random_int(-self::SLEEP_JITTER_MS, self::SLEEP_JITTER_MS);
                usleep(max(0, $sleepMs) * 1000);
            }
        }

        if (empty($failures)) {
            return back()->with('success', "WhatsApp notifications queued to $sent recipient(s).");
        }
        return back()->with('error', "Queued $sent / $total. Some batches failed. Check logs for details.");
    }

    /* ===== Helpers ===== */

    private function toMsisdn(?string $raw, string $defaultCcDigits): ?string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);
        if ($raw !== '' && $raw[0] === '+' && strlen($digits) >= 11) return $digits;                 // +CC########
        if (strlen($digits) === 10)                                   return $defaultCcDigits.$digits; // local 10
        if (strlen($digits) === 11 && $digits[0] === '0')             return $defaultCcDigits.substr($digits,1);
        if (strlen($digits) >= 11)                                    return $digits;                  // already CC
        return null;
    }

    private function sanitizeOneLine(string $s): string
    {
        $s = str_replace(["\r","\n"], ' ', $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    private function normalizeButtonParam(string $input): string
    {
        // Accepts either token (ABC123) or full URL (https://.../ABC123); returns ONLY the token for {{1}}
        $clean = $this->sanitizeOneLine($input);
        if ($clean === '') return '';
        if (preg_match('~^https?://[^/]+/(.+)$~i', $clean, $m)) {
            $clean = $m[1];
        }
        $clean = ltrim($clean, " /");
        $clean = preg_replace('/\s+/', '-', $clean);
        return trim($clean);
    }

}
