<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        // ...
    ],

    'google' => [
        // Client ID dari Google Cloud Console
        'client_id' => env('GOOGLE_CLIENT_ID'),
        // ↑ env() membaca nilai dari file .env

        // Client Secret dari Google Cloud Console
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        // URL callback (harus didaftarkan di Google Console)
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
];