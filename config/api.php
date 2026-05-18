<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the base URL for API requests. This can be overridden per
    | environment using the API_BASE_URL environment variable.
    |
    */

    'base_url' => env('API_BASE_URL', 'https://api.example.com/api'),

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | Default timeout for API requests in seconds
    |
    */

    'timeout' => env('API_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | API Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for retrying failed API requests
    |
    */

    'retry' => [
        'times' => env('API_RETRY_TIMES', 3),
        'delay' => env('API_RETRY_DELAY', 1000), // in milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | API Headers
    |--------------------------------------------------------------------------
    |
    | Default headers to be sent with every API request
    |
    */

    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ],
];
