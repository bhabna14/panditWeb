<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Msg91WhatsappService
{
    protected Client $http;
    protected string $authkey;
    protected string $sender;
    protected string $mode; // template|flow
    protected ?string $namespace;
    protected ?string $template;
    protected ?string $flowId;
    protected string $endpoint;

    public function __construct()
    {
        $this->http      = new Client(['timeout' => 20]);
        $this->authkey   = (string) env('MSG91_AUTHKEY', '');
        $this->sender    = (string) env('MSG91_WA_NUMBER', '');
        $this->mode      = strtolower((string) env('MSG91_WA_MODE', 'template')); // template|flow
        $this->namespace = env('MSG91_WA_NAMESPACE');
        $this->template  = env('MSG91_WA_TEMPLATE');
        $this->flowId    = env('MSG91_WA_FLOW_ID');
        $this->endpoint  = (string) env('MSG91_WA_ENDPOINT', 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message');
    }

    /**
     * @param string      $to         E.164 number (+91xxxxxxxxxx)
     * @param array       $bodyParams Template variables in order (we’ll trim to max 10)
     * @param string|null $mediaUrl   Publicly accessible image URL (optional)
     * @return array{http_status:int, json?:array, body?:string}
     */
    public function sendTemplate(string $to, array $bodyParams, ?string $mediaUrl = null): array
    {
        $params = array_values(array_filter(array_map('strval', $bodyParams), fn($s) => $s !== ''));

        // Cap to 10 template params (adjust if your template needs more)
        $params = array_slice($params, 0, (int) env('MSG91_WA_BODY_PARAM_COUNT', 10));

        $headers = [
            'authkey'       => $this->authkey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];

        // Build payload for TEMPLATE mode (namespace + name)
        if ($this->mode === 'template') {
            $components = [];

            // Body params (text)
            if (!empty($params)) {
                $components[] = [
                    'type'       => 'body',
                    'parameters' => array_map(fn($t) => ['type' => 'text', 'text' => $t], $params),
                ];
            }

            // Optional header image
            if ($mediaUrl) {
                $components[] = [
                    'type'       => 'header',
                    'parameters' => [[
                        'type'  => 'image',
                        'image' => ['link' => $mediaUrl],
                    ]],
                ];
            }

            $payload = [
                'to'       => $to,
                'from'     => $this->sender,
                'type'     => 'template',
                'template' => [
                    'namespace' => $this->namespace,
                    'name'      => $this->template,
                    'language'  => ['policy' => 'deterministic', 'code' => env('MSG91_WA_LANG', 'en')],
                    'components'=> $components,
                ],
            ];
        }
        // FLOW mode (if you’re using MSG91 Flow templates)
        else {
            $payload = [
                'to'      => $to,
                'from'    => $this->sender,
                'type'    => 'flow',
                'flow_id' => $this->flowId,
                'params'  => $params,
            ];

            if ($mediaUrl) {
                $payload['media'] = [
                    'type' => 'image',
                    'url'  => $mediaUrl,
                ];
            }
        }

        try {
            $res = $this->http->post($this->endpoint, [
                'headers' => $headers,
                'json'    => $payload,
            ]);

            $status = $res->getStatusCode();
            $body   = (string) $res->getBody();

            $json = null;
            try {
                $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) { /* ignore */ }

            return ['http_status' => $status, 'json' => $json, 'body' => $body];
        } catch (\Throwable $e) {
            Log::error('MSG91 API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
