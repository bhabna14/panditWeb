<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    /** ===== HARD-CODED MSG91 CONFIG (no .env) ===== */
    private const AUTHKEY              = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER    = '919124420330'; // digits only (no +)
    private const SENDER_E164          = '+919124420330'; // optional (info only)
    private const TEMPLATE_NAMESPACE   = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const TEMPLATE_NAME        = 'flower_wp_message';
    private const LANGUAGE_CODE        = 'en_US'; // must match MSG91 template
    private const ENDPOINT_BULK        = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // Template knobs for your HSM:
    // If your template body has placeholders: set to 0/1/2 accordingly.
    private const BODY_FIELDS          = 1;      // you said earlier you used 1
    private const REQUIRES_URL_PARAM   = true;   // button_1 -> url -> {{1}} required
    private const BUTTON_BASE          = '';     // if MSG91 has a fixed base, e.g., https://example.com/go/

    /** ============================================ */

    protected Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 25]);
    }

    /** Public getters (used by controller/view) */
    public static function integratedNumber(): string { return self::INTEGRATED_NUMBER; }
    public static function languageCode(): string     { return self::LANGUAGE_CODE; }
    public static function bodyFields(): int          { return self::BODY_FIELDS; }
    public static function requiresUrlParam(): bool   { return self::REQUIRES_URL_PARAM; }
    public static function buttonBase(): string       { return self::BUTTON_BASE; }
    public static function senderE164(): string       { return self::SENDER_E164; }

    /**
     * Send bulk template message using the hard-coded config above.
     * $to => array of MSISDN strings (digits only; with country code).
     * $components => associative array like:
     * [
     *   'body_1'   => ['type'=>'text','value'=>'...'], (optional based on BODY_FIELDS)
     *   'body_2'   => ['type'=>'text','value'=>'...'], (optional)
     *   'button_1' => ['subtype'=>'url','type'=>'text','value'=>'<TOKEN for {{1}}>'] (if required)
     * ]
     */
    public function sendBulkTemplate(array $to, array $components): array
    {
        // Clean & uniq numbers
        $to = array_values(array_unique(array_map(
            fn($n) => preg_replace('/\D+/', '', (string)$n),
            $to
        )));

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
                        'to'         => $to,
                        'components' => $components,
                    ]],
                ],
            ],
        ];

        $headers = [
            'authkey'      => self::AUTHKEY,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $res  = $this->http->post(self::ENDPOINT_BULK, ['headers' => $headers, 'json' => $payload]);
            $code = $res->getStatusCode();
            $body = (string) $res->getBody();

            $json = null;
            try { $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) {}

            if ($json && isset($json['errors'])) {
                Log::warning('MSG91 logical errors', ['errors' => $json['errors']]);
            }

            return ['http_status' => $code, 'json' => $json, 'body' => $body];
        } catch (\Throwable $e) {
            Log::error('MSG91 bulk API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
