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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'currency' => env("CASHIER_CURRENCY"),

    'github' => [
        'client_id' => '6437af7f8577c7d106fd',
        'client_secret' => 'f0d11e3c8876a26a563da484bdb851173c66f23e',
        'redirect' => 'http://127.0.0.1:8000/auth/github/callback',
    ],
    'google' => [
        'client_id' => '655210026855-a3qb2tsc4ggf4bsnitgffu8evbmhkqk9.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://127.0.0.1:8000/auth/google/callback',
    ],
];
