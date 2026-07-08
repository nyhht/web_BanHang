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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'vietqr' => [
        'bank_bin' => env('VIETQR_BANK_BIN'),
        'account_number' => env('VIETQR_ACCOUNT_NUMBER'),
        'account_name' => env('VIETQR_ACCOUNT_NAME', env('APP_NAME', 'Mealkit')),
        'template' => env('VIETQR_TEMPLATE', 'compact2'),
        'image_base_url' => env('VIETQR_IMAGE_BASE_URL', 'https://img.vietqr.io/image'),
    ],

    'google_maps' => [
        'browser_key' => env('GOOGLE_MAPS_BROWSER_KEY', env('GOOGLE_MAPS_API_KEY')),
        'server_key' => env('GOOGLE_MAPS_SERVER_KEY', env('GOOGLE_MAPS_API_KEY')),
        'store_latitude' => (float) env('STORE_LATITUDE', 20.918601),
        'store_longitude' => (float) env('STORE_LONGITUDE', 105.762511),
        'max_delivery_distance_km' => (float) env('MAX_DELIVERY_DISTANCE_KM', 40),
        'base_fee' => (float) env('DELIVERY_BASE_FEE', 10000),
        'base_distance_km' => (float) env('DELIVERY_BASE_DISTANCE_KM', 3),
        'per_km_fee' => (float) env('DELIVERY_PER_KM_FEE', 5000),
        'fallback_road_multiplier' => (float) env('DELIVERY_FALLBACK_ROAD_MULTIPLIER', 1.3),
    ],

];
