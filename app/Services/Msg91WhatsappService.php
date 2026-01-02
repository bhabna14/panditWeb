<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    /** ===== MSG91 CONFIG (keep here or move to .env) ===== */
    private const AUTHKEY            = '425546AOXNCrBOzpq6878de9cP1';
    private const INTEGRATED_NUMBER  = '919124420330';   // digits only (no +)
    private const SENDER_E164        = '+919124420330';  // for display/help only

    // Your Utility template configuration
    private const TEMPLATE_NAMESPACE = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const TEMPLATE_NAME      = 'subscription_renewal'; // ✅ UPDATED
    private const LANGUAGE_CODE      = 'en_US';                // ✅ UPDATED

    private const ENDPOINT_BULK      = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // URL param/button support (not used now, but kept for future)
    private const REQUIRES_URL_PARAM = false;
    private const BUTTON_BASE        = '';
    /** ==================================== */

    protected Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 25]);
    }

    // Helpers for controller/view
    public static function integratedNumber(): string { return self::INTEGRATED_NUMBER; }
    public static function languageCode(): string     { return self::LANGUAGE_CODE; }
    public static function requiresUrlParam(): bool   { return self::REQUIRES_URL_PARAM; }
    public static function buttonBase(): string       { return self::BUTTON_BASE; }
    public static function senderE164(): string       { return self::SENDER_E164; }
    public static function templateName(): string     { return self::TEMPLATE_NAME; }

    /**
     * Send bulk template (subscription_renewal) with MSG91 "bulk" endpoint, using:
     *   - body_1
     *   - body_2
     *
     * @param array  $to      MSISDN numbers (digits only, with country code)
     * @param string $body1   -> body_1 value
     * @param string $body2   -> body_2 value
     *
     * @return array ['http_status' => int, 'json' => ?array, 'body' => string]
     * @throws \Throwable
     */
    public function sendBulkTemplate(array $to, string $body1, string $body2): array
    {
        // Normalize recipients: keep digits only, unique
        $to = array_values(array_unique(array_map(
            fn($n) => preg_replace('/\D+/', '', (string)$n),
            $to
        )));

        $components = [
            'body_1' => [
                'type'  => 'text',
                'value' => (string)$body1,
            ],
            'body_2' => [
                'type'  => 'text',
                'value' => (string)$body2,
            ],
        ];

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
                    'to_and_components' => [
                        [
                            'to'         => $to,
                            'components' => $components,
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            'authkey'      => self::AUTHKEY,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $res  = $this->http->post(self::ENDPOINT_BULK, [
                'headers' => $headers,
                'json'    => $payload,
            ]);

            $code = $res->getStatusCode();
            $body = (string)$res->getBody();

            $json = null;
            try {
                $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                // ignore JSON error
            }

            if ($json && isset($json['errors'])) {
                Log::warning('MSG91 logical errors', ['errors' => $json['errors']]);
            }

            return [
                'http_status' => $code,
                'json'        => $json,
                'body'        => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('MSG91 bulk API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
