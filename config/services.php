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

    'sipay' => [
        'base_url' => env('SIPAY_BASE_URL', 'https://payment.baliprov.dev'),
        'username' => env('SIPAY_USERNAME'),
        'password' => env('SIPAY_PASSWORD'),
        'app_code' => env('SIPAY_APP_CODE'),
        'unit_id' => env('SIPAY_UNIT_ID'),
        'pbbkb_pelimpahan_id' => env('SIPAY_PBBKB_PELIMPAHAN_ID'),
        'sanksi_pelimpahan_id' => env('SIPAY_SANKSI_PELIMPAHAN_ID'),
    ],
];
