<?php

// config for LowB/LineSignup
return [
    'line_login' => [
        'channel_id' => env('LINE_LOGIN_CHANNEL_ID'),
        'channel_secret' => env('LINE_LOGIN_CHANNEL_SECRET'),
        'callback_url' => env('LINE_LOGIN_CALLBACK_URL'),
    ],
];
