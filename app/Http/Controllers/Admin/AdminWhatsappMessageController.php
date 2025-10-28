<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client as HttpClient;
use App\Models\User;
use App\Jobs\SendWhatsappTemplateJob;

class AdminWhatsappMessageController extends Controller
{
    // ==== CONFIG (put real keys) ====
    private const MSG91_AUTHKEY       = 'PASTE_YOUR_REAL_MSG91_AUTHKEY';
    private const INTEGRATED_NUMBER   = '919124420330'; // digits only

    private const TEMPLATE_NAMESPACE  = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const LANGUAGE_CODE       = 'en_US';
    private const ENDPOINT_BULK       = 'https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // Approved template names
    private const TEMPLATE_MARKETING  = 'flower_wp_message';
    private const TEMPLATE_UTILITY    = 'flower_utility_update'; // set an approved utility template or leave empty

    // Business rules
    private const MARKETING_COOLDOWN_DAYS = 7;   // per-user cadence
    private const DEFAULT_CC              = '91';
    private const URL_BUTTON_HAS_VAR      = true; // {{1}} exists
    private const BATCH_SIZE              = 1;
    private const MAX_PER_RUN             = 50;
    private const SLEEP_MIN_MS            = 2000;
    private const SLEEP_MAX_MS            = 6000;

    // 131049 handling
    private const CODE_131049               = '131049';
    private const STOP_AFTER_CONSEC_131049  = 5;
    private const INITIAL_BACKOFF_HOURS     = 6;
    private const BACKOFF_MULTIPLIER        = 2.0;
    private const BACKOFF_MAX_HOURS         = 24;

    private static function cooldownKey(string $msisdn): string { return "wa:cooldown:$msisdn"; }
    private static function backoffKey(string $msisdn): string  { return "wa:backoff:$msisdn"; }

    public function whatsappSend(Request $request)
    {
        // guardrails
        if (!is_string(self::MSG91_AUTHKEY) || strlen(trim(self::MSG91_AUTHKEY)) < 20) {
            return back()->with('error', 'MSG91 Authkey not configured.')->withInput();
        }

        $v = $request->validate([
            'audience'         => ['required', Rule::in(['all','selected'])],
            'user'             => ['nullable','array'],
            'user.*'           => ['nullable','string'],
            'title'            => ['required','string','max:255'],
            'description'      => ['required','string'],
            'button_url_value' => [self::URL_BUTTON_HAS_VAR ? 'required' : 'nullable','string','max:2000'],
            'message_type'     => ['nullable', Rule::in(['marketing','utility'])],
        ]);

        // recipients
        $raw = $v['audience'] === 'all'
            ? User::query()
                ->whereNotNull('mobile_number')
                ->where('mobile_number','!=','')
                ->where('opted_out_whatsapp', false)
                ->pluck('mobile_number','id')->all()
            : collect($v['user'] ?? [])->filter()->unique()->values()->all();

        // normalize
        $idByMsisdn = [];
        if ($v['audience'] === 'all') {
            // $raw is [id => mobile]; keep mapping
            foreach ($raw as $uid => $num) {
                $msisdn = $this->toMsisdn($num, self::DEFAULT_CC);
                if ($msisdn) $idByMsisdn[$msisdn] = (string)$uid;
            }
        } else {
            // no id mapping here; best-effort without user IDs
            foreach ($raw as $num) {
                $msisdn = $this->toMsisdn($num, self::DEFAULT_CC);
                if ($msisdn) $idByMsisdn[$msisdn] = null;
            }
        }

        $msisdns = array_keys($idByMsisdn);
        if (!$msisdns) return back()->with('error', 'No valid phone numbers found.')->withInput();

        shuffle($msisdns);
        $msisdns = array_slice($msisdns, 0, self::MAX_PER_RUN);

        // Build components
        $components = [];
        if (self::URL_BUTTON_HAS_VAR) {
            $token = $this->normalizeButtonParam((string)($v['button_url_value'] ?? ''));
            if ($token === '') return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();
            $components['button_1'] = ['subtype'=>'url','type'=>'text','text'=>$token];
        }

        // Which template?
        $type    = $v['message_type'] ?? 'marketing'; // default
        $tplName = $type === 'utility' && self::TEMPLATE_UTILITY ? self::TEMPLATE_UTILITY : self::TEMPLATE_MARKETING;

        // Apply business targeting rules BEFORE calling API
        $eligible = [];
        $suppressed = [];

        if ($v['audience'] === 'all') {
            $users = User::query()->whereIn('id', array_filter(array_values($idByMsisdn)))->get(['id','wa_last_marketing_at','wa_last_inbound_at','opted_out_whatsapp','mobile_number']);
            $byId  = $users->keyBy('id');
            foreach ($msisdns as $n) {
                $uid = $idByMsisdn[$n] ?? null;
                $cooling = Cache::has(self::cooldownKey($n));
                if ($cooling) { $suppressed[] = [$n,'cooldown']; continue; }

                if ($type === 'marketing' && $uid && $byId->has($uid)) {
                    $u = $byId[$uid];
                    // enforce 7-day cadence
                    $okByCadence = !$u->wa_last_marketing_at || Carbon::parse($u->wa_last_marketing_at)->lt(now()->subDays(self::MARKETING_COOLDOWN_DAYS));
                    if (!$okByCadence) { $suppressed[] = [$n,'cadence']; continue; }

                    // bonus filter: if you want only engaged (inbound in last 30d)
                    // $engaged = $u->wa_last_inbound_at && Carbon::parse($u->wa_last_inbound_at)->gt(now()->subDays(30));
                    // if (!$engaged) { $suppressed[] = [$n,'not_engaged']; continue; }
                }

                $eligible[] = $n;
            }
        } else {
            foreach ($msisdns as $n) {
                if (Cache::has(self::cooldownKey($n))) { $suppressed[] = [$n,'cooldown']; continue; }
                $eligible[] = $n;
            }
        }

        if (!$eligible) {
            $why = collect($suppressed)->groupBy(1)->map->count()->map(fn($c,$k)=>"$k:$c")->values()->implode(', ');
            return back()->with('error', "No recipients eligible (suppressed: $why).")->withInput();
        }

        $client  = new HttpClient(['timeout'=>25]);
        $headers = ['authkey'=> self::MSG91_AUTHKEY, 'Accept'=>'application/json', 'Content-Type'=>'application/json'];

        $queued        = 0;
        $failures      = [];
        $consec131049  = 0;

        foreach (array_chunk($eligible, self::BATCH_SIZE) as $i => $chunk) {
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
                            'components' => $components,
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
                    foreach ($chunk as $msisdn) {
                        $this->applyCooldownAndRetry($msisdn, $tplName, $components, $type);
                    }
                } else {
                    $consec131049 = 0;
                }

                if ($okHttp && (!$json || $okJson)) {
                    $queued += count($chunk);

                    // Mark marketing send timestamp for cadence
                    if ($type === 'marketing') {
                        // If we know a user id, set wa_last_marketing_at
                        foreach ($chunk as $msisdn) {
                            $uid = $idByMsisdn[$msisdn] ?? null;
                            if ($uid) {
                                User::where('id', $uid)->update(['wa_last_marketing_at' => now()]);
                            }
                        }
                    }
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
                Log::warning('Stopping due to repeated 131049', ['consecutive_131049'=>$consec131049, 'batches_sent'=>$i+1]);
                break;
            }

            if ($i < ceil(count($eligible) / self::BATCH_SIZE) - 1) {
                usleep(random_int(self::SLEEP_MIN_MS, self::SLEEP_MAX_MS) * 1000);
            }
        }

        if (empty($failures)) {
            return back()->with('success', "WhatsApp queued to $queued / ".count($eligible)." eligible recipients. Suppressed: ".count($suppressed));
        }

        // summarize
        $reasonCounts = [];
        foreach ($failures as $f) {
            $k = (string)($f['reason'] ?? 'send_failed');
            $reasonCounts[$k] = ($reasonCounts[$k] ?? 0) + 1;
        }
        arsort($reasonCounts);
        $top = array_slice(array_map(fn($k,$v)=>"$k ($v)", array_keys($reasonCounts), $reasonCounts), 0, 3);

        return back()->with('error', "Queued $queued / ".count($eligible).". Suppressed: ".count($suppressed).". Issues: ".implode('; ', $top).". Check logs.");
    }

    private function applyCooldownAndRetry(string $msisdn, string $tplName, array $components, string $messageType): void
    {
        $prev   = (int) (Cache::get(self::backoffKey($msisdn)) ?? 0);
        $hours  = $prev > 0 ? min((int)ceil($prev * self::BACKOFF_MULTIPLIER), self::BACKOFF_MAX_HOURS) : self::INITIAL_BACKOFF_HOURS;

        Cache::put(self::cooldownKey($msisdn), now()->addHours($hours)->timestamp, now()->addHours($hours));
        Cache::put(self::backoffKey($msisdn),  $hours, now()->addDays(2));

        SendWhatsappTemplateJob::dispatch(
            $msisdn,
            $tplName,
            $components,
            $messageType,
            self::MSG91_AUTHKEY,
            self::INTEGRATED_NUMBER,
            self::TEMPLATE_NAMESPACE,
            self::LANGUAGE_CODE,
            self::ENDPOINT_BULK
        )->delay(now()->addHours($hours));
    }

    // utils
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
        $clean = $this->oneLine($input);
        if ($clean === '') return '';
        if (preg_match('~^https?://[^/]+/(.+)$~i', $clean, $m)) $clean = $m[1];
        $clean = ltrim($clean, " /");
        $clean = preg_replace('/\s+/', '-', $clean);
        return trim($clean);
    }
}
