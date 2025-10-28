<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    protected Client $http;
    protected string $authkey;
    protected string $endpointBulk;   // bulk endpoint
    protected string $integratedNumber; // digits, e.g. 91912...

    public function __construct(
        string $authkey,
        string $integratedNumber,
        string $endpointBulk = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/'
    ) {
        $this->http             = new Client(['timeout' => 25]);
        $this->authkey          = $authkey;
        $this->integratedNumber = preg_replace('/\D+/', '', $integratedNumber);
        $this->endpointBulk     = $endpointBulk;
    }

    public function sendBulkTemplate(
        array $to,
        array $components,
        ?string $templateName = null,
        ?string $namespace = null,
        ?string $languageCode = 'en_GB'
    ): array {
        if (!$templateName || !$namespace || !$this->integratedNumber) {
            throw new \RuntimeException('MSG91 config missing: template/namespace/integrated number.');
        }

        $payload = [
            'integrated_number' => $this->integratedNumber,
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
