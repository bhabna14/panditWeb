<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],
    'shopify' => [
        'api_url' => env('SHOPIFY_API_URL'),
        'api_key' => env('SHOPIFY_API_KEY'),
        'api_password' => env('SHOPIFY_API_PASSWORD'),
    ],
    // 'firebase' => [
    //     'credentials' => env('FIREBASE_CREDENTIALS_PATH'),
    // ],

    'firebase' => [
        'pandit' => [
            'credentials' => env('FIREBASE_PANDIT_CREDENTIALS_PATH'),
        ],
        'user' => [
            'credentials' => env('FIREBASE_USER_CREDENTIALS_PATH'),
        ],
    ],
'msg91' => [
    'authkey'   => env('MSG91_AUTHKEY'),                    // required
    'wa_number' => env('MSG91_WA_NUMBER'),                  // your approved integrated WA number (e.g. +919124420330)
    'template'  => env('MSG91_WA_TEMPLATE'),                // approved template name
    'namespace' => env('MSG91_WA_NAMESPACE'),               // approved namespace
    'endpoint'  => env('MSG91_WA_ENDPOINT', 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message'),
    'language'  => env('MSG91_WA_LANG', 'en'),

    // === template settings ===
    // total body parameters your template expects (integer: 0,1,2,...)
    'body_param_count' => (int) env('MSG91_WA_BODY_PARAM_COUNT', 2),

    // header media type your template defines: 'none' | 'image' | 'video' | 'document'
    'header_media' => env('MSG91_WA_HEADER_MEDIA', 'none'),
],



];
