<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as HttpClient; // composer require guzzlehttp/guzzle

class AdminWhatsappMessageController extends Controller
{
    // === HARD-CODED CONFIG (NO .env) ===
    private const MSG91_AUTHKEY       = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER   = '919124420330'; // digits only (no +)
    private const TEMPLATE_NAME       = 'flower_wp_message';
    private const TEMPLATE_NAMESPACE  = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const LANGUAGE_CODE       = 'en_US';
    private const ENDPOINT_BULK       = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // === Template knobs (match MSG91 approval) ===
    private const BODY_FIELDS         = 0;     // your template body has 0 params
    private const REQUIRES_URL_PARAM  = true;  // button at index 0 is URL and requires {{1}}
    private const DEFAULT_CC          = '91';  // for 10-digit local numbers

    // === Warmup / Anti-burst controls (reduce 131049) ===
    private const MAX_PER_RUN         = 50;    // hard cap per submission (warm-up). Increase gradually.
    private const BATCH_SIZE          = 1;     // send 1 user per call (safest). Change to 5–20 after warming up.
    private const SLEEP_MIN_MS        = 2000;  // 2s min gap
    private const SLEEP_MAX_MS        = 6000;  // 6s max gap
    private const STOP_AFTER_131049   = 5;     // if we see 5 "131049" in a row, pause further sends

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
            // collected by UI but NOT sent to body (BODY_FIELDS=0)
            'title'            => ['required','string','max:255'],
            'description'      => ['required','string'],
            // required because your template button has {{1}}
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

        // Normalize to MSISDN, de-dupe, hard cap, and randomize order
        $set = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, self::DEFAULT_CC);
            if ($msisdn) $set[$msisdn] = true;
        }
        $toMsisdns = array_keys($set);
        if (!$toMsisdns) {
            return back()->with('error', 'No valid phone numbers found to send.')->withInput();
        }
        shuffle($toMsisdns); // randomize to avoid patterns Meta dislikes
        $toMsisdns = array_slice($toMsisdns, 0, self::MAX_PER_RUN);

        // Build components (no body_* because BODY_FIELDS=0)
        $components = [];
        if (self::REQUIRES_URL_PARAM) {
            $token = $this->normalizeButtonParam((string)($validated['button_url_value'] ?? ''));
            if ($token === '') {
                return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();
            }
            $components['button_1'] = [
                'subtype' => 'url',
                'type'    => 'text',
                'value'   => $token, // only the token for {{1}}
            ];
        }

        $client  = new HttpClient(['timeout'=>25]);
        $headers = [
            'authkey'      => self::MSG91_AUTHKEY,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $total         = count($toMsisdns);
        $queued        = 0;
        $failures      = [];
        $consec131049  = 0;

        // send in small batches (default 1 user/batch)
        foreach (array_chunk($toMsisdns, self::BATCH_SIZE) as $i => $chunk) {
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
                $res  = $client->post(self::ENDPOINT_BULK, ['headers'=>$headers, 'json'=>$payload]);
                $code = $res->getStatusCode();
                $body = (string) $res->getBody();

                $json = null;
                try { $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) {}

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

                // track 131049 specifically if MSG91 bubbles it up in "message"/"errors"
                $reasonStr = (string)($reason ?? '');
                if (strpos($reasonStr, '131049') !== false) {
                    $consec131049++;
                } else {
                    $consec131049 = 0; // reset streak on any non-131049 response
                }

                if ($okHttp && (!$json || $okJson)) {
                    $queued += count($chunk);
                } else {
                    $failures[] = [
                        'batch'  => $i+1,
                        'http'   => $code,
                        'reason' => $reasonStr !== '' ? $reasonStr : 'send_failed',
                        'resp'   => $json ?? $body,
                        'to'     => $chunk,
                    ];
                    Log::warning('MSG91 WA batch failed', end($failures));
                }
            } catch (\Throwable $e) {
                $consec131049 = 0; // network/other error is not a quality throttle
                $failures[] = ['batch'=>$i+1, 'http'=>0, 'reason'=>$e->getMessage(), 'to'=>$chunk];
                Log::error('MSG91 WA exception', ['batch'=>$i+1, 'error'=>$e->getMessage(), 'to'=>$chunk]);
            }

            // If we hit repeated quality throttles, stop early to protect sender rating
            if ($consec131049 >= self::STOP_AFTER_131049) {
                Log::warning('Stopping early due to repeated 131049 throttles', [
                    'consecutive_131049' => $consec131049,
                    'batches_sent'       => $i+1,
                ]);
                break;
            }

            // anti-burst: small randomized gap between batches
            if ($i < ceil($total / self::BATCH_SIZE) - 1) {
                $sleepMs = random_int(self::SLEEP_MIN_MS, self::SLEEP_MAX_MS);
                usleep($sleepMs * 1000);
            }
        }

        if (empty($failures)) {
            return back()->with('success', "WhatsApp queued to $queued / $total recipients.");
        }

        // Summarize top failure reasons (will likely include 131049)
        $reasonCounts = [];
        foreach ($failures as $f) {
            $k = (string)($f['reason'] ?? 'send_failed');
            $reasonCounts[$k] = ($reasonCounts[$k] ?? 0) + 1;
        }
        arsort($reasonCounts);
        $top = array_slice(array_map(fn($k,$v)=>"$k ($v)", array_keys($reasonCounts), $reasonCounts), 0, 3);

        return back()->with('error', "Queued $queued / $total. Some sends were throttled: ".implode('; ', $top).". Check logs for details.");
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

    private function oneLine(string $s): string
    {
        $s = str_replace(["\r","\n"], ' ', $s);
        return trim(preg_replace('/\s+/u',' ', $s));
    }

    private function normalizeButtonParam(string $input): string
    {
        // Accepts either token (ABC123) or full URL (https://.../ABC123); returns ONLY the token for {{1}}
        $clean = $this->oneLine($input);
        if ($clean === '') return '';
        if (preg_match('~^https?://[^/]+/(.+)$~i', $clean, $m)) {
            $clean = $m[1];
        }
        $clean = ltrim($clean, " /");
        $clean = preg_replace('/\s+/', '-', $clean);
        return trim($clean);
    }
}