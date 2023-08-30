<?php

return [
    'jwt_secret' => env('JWT_SECRET'),

    'jwt_alg' => explode(',', env('JWT_ALG', 'HS256')),

    'jwt_max_lifetime' => env('JWT_MAX_LIFETIME', 60),

    'webhook_url' => env('WEBHOOK_URL'),
];
