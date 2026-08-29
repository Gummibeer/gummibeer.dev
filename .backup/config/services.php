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

    'strava' => [
        'athlete_id' => env('STRAVA_ID', '22896286'),
        'refresh_token' => env('STRAVA_REFRESH_TOKEN'),
        'client_id' => env('STRAVA_CLIENT_ID'),
        'client_secret' => env('STRAVA_CLIENT_SECRET'),
    ],

    'webmention' => [
        'token' => env('WEBMENTION_TOKEN'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => '-1001286676640', // Http::get(sprintf('https://api.telegram.org/bot%s/getUpdates', $token))->json()['result'][0]['channel_post']['chat']['id']
    ],

    'twitter' => [
        'consumer_key' => null,
        'consumer_secret' => null,
        'access_token' => null,
        'access_token_secret' => null,
    ],

];
