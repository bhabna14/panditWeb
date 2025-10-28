<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as HttpClient; // ✅ alias Guzzle

class AdminWhatsappMessageController extends Controller
{
    // === HARD-CODED CONFIG (NO .env) ===
    private const MSG91_AUTHKEY       = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER   = '919124420330'; // digits only
    private const TEMPLATE_NAME       = 'flower_wp_message';
    private const TEMPLATE_NAMESPACE  = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const LANGUAGE_CODE       = 'en_US';
    private const ENDPOINT_BULK       = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // Local knobs
    private const BODY_FIELDS         = 2;      // 0,1,2 — how many body placeholders your template has
    private const REQUIRES_URL_PARAM  = false;  // true only if your template has a URL button with {{1}}
    private const DEFAULT_CC          = '91';   // for 10-digit local numbers

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
            'title'            => ['required','string','max:255'],
            'description'      => ['required','string'],
            'button_url_value' => [self::REQUIRES_URL_PARAM ? 'required' : 'nullable','string','max:2000'],
        ]);

        $title       = trim($validated['title']);
        $description = trim($validated['description']);
        $buttonVar   = isset($validated['button_url_value']) ? trim($validated['button_url_value']) : null;

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

        // Normalize to MSISDN (digits)
        $toMsisdns = [];
        foreach ($rawNumbers as $raw) {
            $msisdn = $this->toMsisdn($raw, self::DEFAULT_CC);
            if ($msisdn !== null) $toMsisdns[] = $msisdn;
        }
        $toMsisdns = array_values(array_unique($toMsisdns));
        if (!$toMsisdns) {
            return back()->with('error', 'No valid phone numbers found to send.')->withInput();
        }

        // Sanitize body values (bulk forbids newlines)
        $titleClean = $this->sanitizeOneLine($title);
        $descClean  = $this->sanitizeOneLine($description);

        // Build template components
        $components = [];
        if (self::BODY_FIELDS === 2) {
            $components['body_1'] = ['type'=>'text', 'value'=>$titleClean];
            $components['body_2'] = ['type'=>'text', 'value'=>$descClean];
        } elseif (self::BODY_FIELDS === 1) {
            $components['body_1'] = ['type'=>'text', 'value'=>$titleClean . ' — ' . $descClean];
        } // else: 0 => no body_* components

        // Optional URL button {{1}}
        if (self::REQUIRES_URL_PARAM || ($buttonVar !== null && $buttonVar !== '')) {
            $param = $this->normalizeButtonParam((string)$buttonVar, '');
            if (self::REQUIRES_URL_PARAM && $param === '') {
                return back()->with('error', 'URL button requires a parameter (template has {{1}}).')->withInput();
            }
            if ($param !== '') {
                $components['button_1'] = [
                    'subtype' => 'url',
                    'type'    => 'text',
                    'value'   => $param,
                ];
            }
        }

        // MSG91 bulk payload
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
                        'to'         => array_map(fn($n) => preg_replace('/\D+/', '', $n), $toMsisdns),
                        'components' => $components,
                    ]],
                ],
            ],
        ];

        try {
            $client  = new HttpClient(['timeout'=>25]); // ✅ use Guzzle
            $headers = [
                'authkey'      => self::MSG91_AUTHKEY,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ];

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
                return back()->with('success', 'WhatsApp notifications queued to '.count($toMsisdns).' recipient(s).');
            }

            Log::error('MSG91 bulk WA failed', ['http_status'=>$code, 'json'=>$json, 'body'=>$body]);
            return back()->with('error', 'MSG91 bulk send failed: ' . ($reason ?: 'send_failed'));

        } catch (\Throwable $e) {
            Log::error('MSG91 bulk WA exception', ['error'=>$e->getMessage()]);
            return back()->with('error', 'MSG91 bulk send error: '.$e->getMessage());
        }
    }

    /* ===== Helpers ===== */

    private function toMsisdn(?string $raw, string $defaultCcDigits): ?string
    {
        $raw    = (string) $raw;
        $digits = preg_replace('/\D+/', '', $raw);
        if ($raw !== '' && $raw[0] === '+' && strlen($digits) >= 11) return $digits;                 // +CC######## -> digits
        if (strlen($digits) === 10)                                   return $defaultCcDigits.$digits; // local 10
        if (strlen($digits) === 11 && $digits[0] === '0')             return $defaultCcDigits.substr($digits,1);
        if (strlen($digits) >= 11)                                    return $digits;                  // already with CC
        return null;
    }

    private function sanitizeOneLine(string $s): string
    {
        $s = str_replace(["\r", "\n"], ' ', $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    private function normalizeButtonParam(string $input, string $base): string
    {
        $clean = $this->sanitizeOneLine($input);
        if ($clean === '') return '';
        if ($base !== '') {
            $baseNorm = rtrim($base, '/') . '/';
            if (stripos($clean, $baseNorm) === 0) $clean = substr($clean, strlen($baseNorm));
        }
        $clean = ltrim($clean, " /");
        $clean = preg_replace('/\s+/', '-', $clean);
        return trim($clean);
    }
}
