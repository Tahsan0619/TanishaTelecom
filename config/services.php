<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'shurjopay' => [
        // "sandbox" for testing, "live" once shurjoPay activates the live credentials.
        'mode' => env('SHURJOPAY_MODE', 'sandbox'),
        'prefix' => env('SHURJOPAY_PREFIX', 'TTM'),
        // Used when a customer checks out without an email address (the gateway requires one).
        'fallback_email' => env('SHURJOPAY_FALLBACK_EMAIL', 'customer@tanishatelecom.com'),

        'sandbox' => [
            'url' => env('SHURJOPAY_SANDBOX_URL', 'https://sandbox.shurjopayment.com'),
            'username' => env('SHURJOPAY_SANDBOX_USERNAME'),
            'password' => env('SHURJOPAY_SANDBOX_PASSWORD'),
        ],

        'live' => [
            'url' => env('SHURJOPAY_LIVE_URL', 'https://engine.shurjopayment.com'),
            'username' => env('SHURJOPAY_LIVE_USERNAME'),
            'password' => env('SHURJOPAY_LIVE_PASSWORD'),
        ],
    ],

];
