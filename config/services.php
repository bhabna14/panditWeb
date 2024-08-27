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


];
