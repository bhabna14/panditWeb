<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    /** ===== HARD-CODED MSG91 CONFIG (no .env) ===== */
    private const AUTHKEY              = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER    = '919124420330';            // digits only (no +)
    private const SENDER_E164          = '+919124420330';           // for display/help only
    private const TEMPLATE_NAMESPACE   = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const TEMPLATE_NAME        = 'flower_wp_message';       // update if you duplicated template
    private const LANGUAGE_CODE        = 'en_US';
    private const ENDPOINT_BULK        = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // Your template must have 0 placeholders (body/buttons). We will send no components.
    private const BODY_FIELDS          = 0;     // 0 == don’t send body_*
    private const REQUIRES_URL_PARAM   = false; // removed
    private const BUTTON_BASE          = '';    // not used

    /** ============================================ */

    protected Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 25]);
    }

    // Helpers for controller/view
    public static function integratedNumber(): string { return self::INTEGRATED_NUMBER; }
    public static function languageCode(): string     { return self::LANGUAGE_CODE; }
    public static function bodyFields(): int          { return self::BODY_FIELDS; }
    public static function requiresUrlParam(): bool   { return self::REQUIRES_URL_PARAM; }
    public static function buttonBase(): string       { return self::BUTTON_BASE; }
    public static function senderE164(): string       { return self::SENDER_E164; }

    /**
     * Send bulk template without components (0 params).
     */
    public function sendBulkTemplate(array $to): array
    {
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
                    // IMPORTANT: No components if 0 params expected
                    'to_and_components' => [[ 'to' => $to ]],
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
