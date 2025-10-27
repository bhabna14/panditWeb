<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    protected Client $http;
    protected string $authkey;
    protected string $sender;        // E.164 (e.g. +91912...)
    protected string $endpointBulk;  // bulk endpoint

    public function __construct()
    {
        $this->http        = new Client(['timeout' => 25]);
        $this->authkey     = (string) env('MSG91_AUTHKEY', '');
        $this->sender      = (string) env('MSG91_WA_NUMBER', ''); // +<cc><number>
        $this->endpointBulk= (string) env('MSG91_WA_BULK_ENDPOINT', 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/');
    }

    /**
     * MSISDN digits form of integrated number (no '+').
     * Priority: MSG91_WA_INTEGRATED_NUMBER (already digits) -> derived from MSG91_WA_NUMBER.
     */
    public function integratedNumber(): string
    {
        $env = (string) env('MSG91_WA_INTEGRATED_NUMBER', '');
        if ($env !== '') {
            return preg_replace('/\D+/', '', $env);
        }
        $digits = preg_replace('/\D+/', '', $this->sender);
        return ltrim($digits, '+');
    }

    /**
     * Send a bulk template message as per MSG91 cURL structure.
     *
     * @param string[] $to           MSISDNs (digits only, with country code, no '+')
     * @param array    $components   e.g. ['body_1'=>['type'=>'text','value'=>'...'], 'button_1'=>['subtype'=>'url','type'=>'text','value'=>'...']]
     * @param string   $templateName MSG91 approved template name
     * @param string   $namespace    MSG91 namespace (UUID-like)
     * @param string   $languageCode e.g. 'en_GB' / 'en_US'
     * @param string   $integratedNumber digits only (with country code, no '+')
     * @return array{http_status:int, json?:array, body?:string}
     */
    public function sendBulkTemplate(
        array $to,
        array $components,
        ?string $templateName = null,
        ?string $namespace = null,
        ?string $languageCode = null,
        ?string $integratedNumber = null
    ): array {
        $templateName     = $templateName     ?: (string) env('MSG91_WA_TEMPLATE', '');
        $namespace        = $namespace        ?: (string) env('MSG91_WA_NAMESPACE', '');
        $languageCode     = $languageCode     ?: (string) env('MSG91_WA_LANG_CODE', 'en_GB');
        $integratedNumber = $integratedNumber ?: $this->integratedNumber();

        // Build JSON exactly like the docs/sample
        $payload = [
            'integrated_number' => $integratedNumber,
            'content_type'      => 'template',
            'payload'           => [
                'messaging_product' => 'whatsapp',
                'type'              => 'template',
                'template'          => [
                    'name'      => $templateName,
                    'language'  => [
                        'code'   => $languageCode,
                        'policy' => 'deterministic',
                    ],
                    'namespace' => $namespace,
                    'to_and_components' => [[
                        'to'         => array_values(array_unique(array_map(fn($n) => preg_replace('/\D+/', '', $n), $to))),
                        'components' => $components,
                    ]],
                ],
            ],
        ];

        $headers = [
            'authkey'      => $this->authkey,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $res  = $this->http->post($this->endpointBulk, ['headers'=>$headers, 'json'=>$payload]);
            $code = $res->getStatusCode();
            $body = (string) $res->getBody();

            $json = null;
            try { $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) {}

            // Bubble server-indicated logical errors for visibility
            if ($json && isset($json['errors'])) {
                Log::warning('MSG91 logical errors', ['errors'=>$json['errors']]);
            }

            return ['http_status'=>$code, 'json'=>$json, 'body'=>$body];
        } catch (\Throwable $e) {
            Log::error('MSG91 bulk API error', ['error'=>$e->getMessage()]);
            throw $e;
        }
    }
}
