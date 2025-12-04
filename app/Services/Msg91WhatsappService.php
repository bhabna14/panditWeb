<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    /** ===== HARD-CODED MSG91 CONFIG (NO .env IN THIS EXAMPLE) ===== */
    private const AUTHKEY            = '425546AOXNCrBOzpq6878de9cP1';      // <-- put your MSG91 authkey
    private const INTEGRATED_NUMBER  = '919124420330';                 // digits only (no +)
    private const SENDER_E164        = '+919124420330';                // for display/help only

    // Template configuration (MATCH your MSG91 template setup)
    private const TEMPLATE_NAMESPACE = '73669fdc_d75e_4db4_a7b8_1cf1ed246b43';
    private const TEMPLATE_NAME      = '33_crores';                    // <--- from your curl
    private const LANGUAGE_CODE      = 'en';                           // <--- from your curl

    private const ENDPOINT_BULK      = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    // Template parameters count; 0 = no body/button variables
    private const BODY_FIELDS        = 0;

    // URL param/button support (not used here, but kept for future if you need it)
    private const REQUIRES_URL_PARAM = false;
    private const BUTTON_BASE        = '';

    /** ============================================================ */

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
    public static function templateName(): string     { return self::TEMPLATE_NAME; }

    /**
     * Send bulk template with MSG91 "bulk" endpoint.
     * Currently assumes NO template parameters (components is {}).
     *
     * @param array $to Array of MSISDN numbers (digits only, with country code)
     * @return array ['http_status' => int, 'json' => ?array, 'body' => string]
     * @throws \Throwable
     */
    public function sendBulkTemplate(array $to): array
    {
        // Normalize: only keep digits, unique
        $to = array_values(array_unique(array_map(
            fn($n) => preg_replace('/\D+/', '', (string)$n),
            $to
        )));

        // Build components. For 0-parameter template, MSG91 expects "components": {}
        // i.e., an empty object, not an empty array.
        if (self::BODY_FIELDS <= 0) {
            $components = (object)[];  // -> "{}" in JSON
        } else {
            // If later you add body variables in your template,
            // you can build the "components" structure here.
            // Example skeleton (commented):
            //
            // $components = [
            //     'body' => [
            //         [
            //             'type'       => 'text',
            //             'parameters' => [
            //                 ['type' => 'text', 'text' => 'Value 1'],
            //                 ['type' => 'text', 'text' => 'Value 2'],
            //             ],
            //         ],
            //     ],
            // ];
            $components = (object)[];
        }

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
                // If decode fails, leave $json = null.
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
