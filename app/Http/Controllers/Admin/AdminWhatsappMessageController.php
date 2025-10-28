<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use GuzzleHttp\Client as HttpClient;
use App\Models\User;
use App\Jobs\SendWhatsappTemplateJob;

class AdminWhatsappMessageController extends Controller
{
    // === HARD-CODED CONFIG (NO .env) ===
    private const MSG91_AUTHKEY       = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER   = '919124420330'; // digits only
    private const TEMPLATE_NAME       = 'flower_wp_message';          // Marketing
    private const TEMPLATE_NAMESPACE  = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const LANGUAGE_CODE       = 'en_US';
    private const ENDPOINT_BULK       = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // If you also have a Utility template (not frequency-capped), set it here:
    private const UTILITY_TEMPLATE_NAME = 'flower_utility_update';    // Optional
    private const USE_UTILITY_FALLBACK  = false;                      // true to fallback

    // Template knobs (match MSG91 approval)
    private const BODY_FIELDS         = 0;     // body has no variables
    private const URL_BUTTON_HAS_VAR  = true;  // button has {{1}} param
    private const DEFAULT_CC          = '91';

    // Send hygiene
    private const MAX_PER_RUN         = 50;    // warm up
    private const BATCH_SIZE          = 1;
    private const SLEEP_MIN_MS        = 2000;
    private const SLEEP_MAX_MS        = 6000;

    // 131049 handling
    private const CODE_131049                 = '131049';
    private const STOP_AFTER_CONSEC_131049    = 5;
    private const INITIAL_BACKOFF_HOURS       = 6;   // 6h, then 12h, then 24h...
    private const BACKOFF_MULTIPLIER          = 2.0;
    private const BACKOFF_MAX_HOURS           = 24;

    // Cache keys
    private static function cooldownKey(string $msisdn): string { return "wa:cooldown:$msisdn"; }
    private static function backoffKey(string $msisdn): string  { return "wa:backoff:$msisdn"; }

    public function whatsappcreate(Request $request)
    {
        $users = User::query()
            ->select('id','name','email','mobile_number')
            ->whereNotNull('mobile_number')->where('mobile_number','!=','')
            ->orderBy('name')->limit(1000)->get();

        return view('admin.fcm-notification.send-whatsaap-notification', compact('users'));
    }

    public function whatsappSend(Request $request)
    {
        $validated = $request->validate([
            'audience'         => ['required', Rule::in(['all','selected'])],
            'user'             => ['nullable','array'],
            'user.*'           => ['nullable','string'],
            'title'            => ['required','string','max:255'],
            'description'      => ['required','string'],
            'button_url_value' => [self::URL_BUTTON_HAS_VAR ? 'required' : 'nullable','string','max:2000'],
            // optional switch if you want to force Utility for this send
            'message_type'     => ['nullable', Rule::in(['marketing','utility'])],
        ]);

        // Resolve recipients
        $rawNumbers = $validated['audience'] === 'all'
            ? User::query()->whereNotNull('mobile_number')->where('mobile_number','!=','')->pluck('mobile_number')->all()
            : array_unique(array_map('trim', $validated['user'] ?? []));

        // Normalize E.164-ish and de-dupe
        $set = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, self::DEFAULT_CC);
            if ($msisdn) $set[$msisdn] = true;
        }
        $toMsisdns = array_keys($set);
        if (!$toMsisdns) return back()->with('error', 'No valid phone numbers found to send.')->withInput();

        shuffle($toMsisdns);
        $toMsisdns = array_slice($toMsisdns, 0, self::MAX_PER_RUN);

        // Build components (button param uses "text", not "value")
        $components = [];
        if (self::URL_BUTTON_HAS_VAR) {
            $token = $this->normalizeButtonParam((string)($validated['button_url_value'] ?? ''));
            if ($token === '') return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();

            $components['button_1'] = [
                'subtype' => 'url',
                'type'    => 'text',
                'text'    => $token,  // <<<<<<<<<< IMPORTANT (not "value")
            ];
        }

        // Decide template: marketing or utility
        $forceType = $validated['message_type'] ?? 'marketing';
        $tplName   = ($forceType === 'utility')
            ? (self::UTILITY_TEMPLATE_NAME ?: self::TEMPLATE_NAME)
            : self::TEMPLATE_NAME;

        // Filter out numbers currently in cooldown (due to earlier 131049)
        $toSend = [];
        foreach ($toMsisdns as $n) {
            if (!Cache::has(self::cooldownKey($n))) $toSend[] = $n;
        }
        if (!$toSend) return back()->with('error', 'All selected recipients are cooling down due to previous 131049 throttles. Try later.')->withInput();

        $client  = new HttpClient(['timeout'=>25]);
        $headers = [
            'authkey'      => self::MSG91_AUTHKEY,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $total         = count($toSend);
        $queued        = 0;
        $failures      = [];
        $consec131049  = 0;

        foreach (array_chunk($toSend, self::BATCH_SIZE) as $i => $chunk) {
            $payload = [
                'integrated_number' => self::INTEGRATED_NUMBER,
                'content_type'      => 'template',
                'payload'           => [
                    'messaging_product' => 'whatsapp',
                    'type'              => 'template',
                    'template'          => [
                        'name'      => $tplName,
                        'language'  => ['code'=> self::LANGUAGE_CODE, 'policy'=> 'deterministic'],
                        'namespace' => self::TEMPLATE_NAMESPACE,
                        'to_and_components' => [[
                            'to'         => array_map(fn($n) => preg_replace('/\D+/', '', $n), $chunk),
                            'components' => $components, // ok even when empty
                        ]],
                    ],
                ],
            ];

            try {
                $res  = $client->post(self::ENDPOINT_BULK, ['headers'=>$headers, 'json'=>$payload]);
                $code = $res->getStatusCode();
                $body = (string) $res->getBody();
                $json = json_decode($body, true);

                $okHttp = ($code >= 200 && $code < 300);
                $okJson = false;
                $reason = null;

                if (is_array($json)) {
                    $statusField = strtolower((string)($json['status'] ?? $json['type'] ?? ''));
                    $okJson = in_array($statusField, ['success','queued','accepted'], true)
                           || ($json['success'] ?? false) === true;
                    $reason = $json['message']
                        ?? ($json['error']['message'] ?? null)
                        ?? ($json['errors'][0]['message'] ?? null)
                        ?? ($json['errors'][0] ?? null)
                        ?? null;
                }

                $reasonStr = (string)($reason ?? '');
                $has131049 = (strpos($reasonStr, self::CODE_131049) !== false);

                if ($has131049) {
                    $consec131049++;
                    // Put all recipients of this batch into cooldown and schedule a retry job
                    foreach ($chunk as $msisdn) {
                        $this->applyCooldownAndRetry($msisdn, $tplName, $components, $forceType);
                    }
                } else {
                    $consec131049 = 0;
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
                $consec131049 = 0;
                $failures[] = ['batch'=>$i+1, 'http'=>0, 'reason'=>$e->getMessage(), 'to'=>$chunk];
                Log::error('MSG91 WA exception', ['batch'=>$i+1, 'error'=>$e->getMessage(), 'to'=>$chunk]);
            }

            if ($consec131049 >= self::STOP_AFTER_CONSEC_131049) {
                Log::warning('Stopping early due to repeated 131049 throttles', [
                    'consecutive_131049' => $consec131049,
                    'batches_sent'       => $i+1,
                ]);
                break;
            }

            if ($i < ceil($total / self::BATCH_SIZE) - 1) {
                $sleepMs = random_int(self::SLEEP_MIN_MS, self::SLEEP_MAX_MS);
                usleep($sleepMs * 1000);
            }
        }

        if (empty($failures)) {
            return back()->with('success', "WhatsApp queued to $queued / $total recipients.");
        }

        // Summarize top failure reasons
        $reasonCounts = [];
        foreach ($failures as $f) {
            $k = (string)($f['reason'] ?? 'send_failed');
            $reasonCounts[$k] = ($reasonCounts[$k] ?? 0) + 1;
        }
        arsort($reasonCounts);
        $top = array_slice(array_map(fn($k,$v)=>"$k ($v)", array_keys($reasonCounts), $reasonCounts), 0, 3);

        return back()->with('error', "Queued $queued / $total. Some sends were throttled: ".implode('; ', $top).". Check logs for details.");
    }

    /* ===== 131049 helpers ===== */

    private function applyCooldownAndRetry(string $msisdn, string $tplName, array $components, string $forceType): void
    {
        // backoff hours (6 -> 12 -> 24 -> 24...)
        $prev   = (int) (Cache::get(self::backoffKey($msisdn)) ?? 0);
        $hours  = $prev > 0 ? min((int)ceil($prev * self::BACKOFF_MULTIPLIER), self::BACKOFF_MAX_HOURS) : self::INITIAL_BACKOFF_HOURS;

        Cache::put(self::cooldownKey($msisdn), now()->addHours($hours)->timestamp, now()->addHours($hours));
        Cache::put(self::backoffKey($msisdn),  $hours, now()->addDays(2));

        // schedule a retry via queue
        SendWhatsappTemplateJob::dispatch(
            $msisdn,
            $tplName,
            $components,
            $forceType,
            self::MSG91_AUTHKEY,
            self::INTEGRATED_NUMBER,
            self::TEMPLATE_NAMESPACE,
            self::LANGUAGE_CODE,
            self::ENDPOINT_BULK
        )->delay(now()->addHours($hours));
    }

    /* ===== Utils ===== */

    private function toMsisdn(?string $raw, string $defaultCcDigits): ?string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);
        if ($raw !== '' && $raw[0] === '+' && strlen($digits) >= 11) return $digits;
        if (strlen($digits) === 10)                                   return $defaultCcDigits.$digits;
        if (strlen($digits) === 11 && $digits[0] === '0')             return $defaultCcDigits.substr($digits,1);
        if (strlen($digits) >= 11)                                    return $digits;
        return null;
    }

    private function oneLine(string $s): string
    {
        $s = str_replace(["\r","\n"], ' ', $s);
        return trim(preg_replace('/\s+/u',' ', $s));
    }

    private function normalizeButtonParam(string $input): string
    {
        // Accept either the token (ABC123) or full URL (.../ABC123); return ONLY the token for {{1}}
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
