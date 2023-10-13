<?php

return [
    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'base_url' => env('BREVO_BASE_URL', 'https://api.brevo.com/v3/'),
    ],
    'api_user_agent' => env('USER_AGENT'),
];
