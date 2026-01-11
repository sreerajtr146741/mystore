<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Allow your frontend domain (Render)
    // Replace this URL with your real frontend URL:
     'allowed_origins' => [
        'https://mystore-frontend-ip77.onrender.com',
        'https://mystore-frontend.onrender.com',
    ],

    // For testing, allow all:
    //'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
