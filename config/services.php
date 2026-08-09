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

    'google_maps' => [
        // Placeholder key carried over from the original static template - billing
        // isn't enabled on it, so the map tiles won't render, but it's enough for
        // window.google to exist so app.js's map init code doesn't crash. Replace
        // with a real key (with billing enabled) before going live.
        'key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyB13ZAvCezMx5TETYIiGlzVIq65Mc2FG5g'),
    ],

    'indexnow' => [
        // Auto-pings Bing/Yandex/Naver/Seznam whenever a blog post is
        // published/updated (App\Services\IndexNowService) - Google does not
        // participate in IndexNow, this covers other engines only. Key must
        // match routes/web.php's {key}.txt verification route and be 8-128
        // hex chars per the IndexNow spec. Generated once for this install;
        // safe to regenerate (php -r "echo bin2hex(random_bytes(16));"),
        // just keep .env and the route in sync.
        'key' => env('INDEXNOW_KEY', '453505b5c8a68deb4904b180d789060a'),
    ],

];
