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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'key'             => env('ANTHROPIC_API_KEY'),
        'url'             => env('ANTHROPIC_API_URL', 'https://api.anthropic.com/v1/messages'),
        'model'           => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
        'use_openai_format' => env('ANTHROPIC_USE_OPENAI_FORMAT', false),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'midtrans' => [
        'server_key'     => env('MIDTRANS_SERVER_KEY'),
        'client_key'     => env('MIDTRANS_CLIENT_KEY'),
        'is_production'  => env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'fonnte' => [
        'token' => env('FONNTE_TOKEN'),
    ],

    'support' => [
        'whatsapp' => env('SUPPORT_WHATSAPP_NUMBER', '6281234567890'),
    ],

    'currency' => [
        'usd_rate' => (float) env('USD_IDR_RATE', 15800),
    ],

    'insurance' => [
        'price_per_ticket' => (float) env('INSURANCE_PRICE_PER_TICKET', 15000),
        'price_per_booking' => (float) env('INSURANCE_PRICE_PER_BOOKING', 25000),
    ],

];
