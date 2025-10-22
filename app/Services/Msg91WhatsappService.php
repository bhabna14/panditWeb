<?php

namespace App\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Msg91WhatsappService
{
    protected Client $http;
    protected string $authkey;
    protected string $endpoint;
    protected string $integratedNumber;
    protected string $templateName;
    protected string $namespace;
    protected string $language;
    protected int $bodyParamCount;
    protected string $headerMedia; // none|image|video|document

    public function __construct()
    {
        $this->authkey          = (string) config('services.msg91.authkey');
        $this->endpoint         = (string) config('services.msg91.endpoint');
        $this->integratedNumber = (string) config('services.msg91.wa_number');
        $this->templateName     = (string) config('services.msg91.template');
        $this->namespace        = (string) config('services.msg91.namespace');
        $this->language         = (string) config('services.msg91.language', 'en');
        $this->bodyParamCount   = (int) config('services.msg91.body_param_count', 2);
        $this->headerMedia      = (string) config('services.msg91.header_media', 'none');

        $this->http = new Client(['timeout' => 25]);
    }

    /**
     * Send a template message to a single WhatsApp number via MSG91.
     *
     * @param string $toE164  E.164-ish phone, e.g. +917008710275
     * @param array  $bodyParams Ordered list of strings to map to template variables ({{1}}, {{2}}, ...)
     * @param string|null $mediaUrl Public URL if template has media header (image/video/document)
     * @return ResponseInterface
     */
    public function sendTemplate(string $toE164, array $bodyParams = [], ?string $mediaUrl = null): ResponseInterface
    {
        // MSG91 usually wants numeric phone without '+'
        $digits = preg_replace('/\D+/', '', $toE164);

        // Build body params according to your template’s expected count
        // If fewer provided, pad with empty; if more provided, trim
        $params = array_slice(array_values($bodyParams), 0, $this->bodyParamCount);
        while (count($params) < $this->bodyParamCount) {
            $params[] = '';
        }

        $bodyParamObjects = array_map(fn($p) => ['text' => (string) $p], $params);

        $payload = [
            'integrated_number' => $this->integratedNumber,
            'content_type'      => 'template',
            'payload'           => [
                'to'   => [['phone_number' => $digits]],
                'type' => 'template',
                'template' => [
                    'template_name' => $this->templateName,
                    'namespace'     => $this->namespace,
                    'language'      => ['policy' => 'deterministic', 'code' => $this->language],
                    'body'          => $bodyParamObjects, // matches {{1}}, {{2}}, ...
                ],
            ],
        ];

        // Attach header media only if template is defined with media AND caller provided URL
        if ($this->headerMedia !== 'none' && $mediaUrl) {
            $payload['payload']['template']['header'] = [
                'type'  => 'media',
                'media' => [
                    'type' => $this->headerMedia, // image|video|document
                    'url'  => $mediaUrl,
                ],
            ];
        }

        return $this->http->post($this->endpoint, [
            'headers' => [
                'authkey'      => $this->authkey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'json' => $payload,
        ]);
    }
}
