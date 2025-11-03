<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | You can configure the API endpoint for production or test environments
    |
    */
    'api_url' => env('SATIM_API_URL', 'https://test.satim.dz/payment/rest'),

    /*
    |--------------------------------------------------------------------------
    | API Username
    |--------------------------------------------------------------------------
    |
    | Username used to connect to the SATIM API
    | To get a username, visit: https://cibweb.dz/fr/
    |
    */
    'username' => env('SATIM_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | API Password
    |--------------------------------------------------------------------------
    |
    | Password used to connect to the SATIM API
    | To get a password, visit: https://cibweb.dz/fr/
    |
    */
    'password' => env('SATIM_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | API Terminal
    |--------------------------------------------------------------------------
    |
    | Terminal ID used to connect to the SATIM API
    | To get a terminal ID, visit: https://cibweb.dz/fr/
    |
    */
    'terminal' => env('SATIM_TERMINAL'),

    /*
    |--------------------------------------------------------------------------
    | API language
    |--------------------------------------------------------------------------
    |
    | The language used in the SATIM API
    | Possible values are: 'AR', 'EN', 'FR'. You can use the SatimLanguage enum
    |
    */
    'language' => env('SATIM_LANGUAGE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | API currency
    |--------------------------------------------------------------------------
    |
    | The currency used to make payments
    | Possible values are: 'EUR', 'DZD', 'USD'. You can use the SatimCurrency enum
    |
    */
    'currency' => env('SATIM_CURRENCY', 'DZD'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | These options are passed directly to the underlying Guzzle HTTP client
    | used by Laravel's HTTP facade.
    |
    | - "verify": Set to false only in development or testing environments.
    | - "allow_redirects": Set to true to automatically follow redirects.
    | - "timeout": The timeout used in the SATIM API requests
    |
    */
    'http_options' => [
        'verify' => env('SATIM_HTTP_VERIFY_SSL', true),
        'allow_redirects' => env('SATIM_HTTP_ALLOW_REDIRECTS', false),
        'timeout' => env('SATIM_HTTP_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Defines how the HTTP client should handle temporary network errors
    | when communicating with the SATIM API.
    |
    | - "retry": The number of retry attempts before failing the request.
    | - "sleeptime": The delay (in milliseconds) between each retry attempt.
    |
    */
    'http_client' => [
        'retry' => env('SATIM_HTTP_CLIENT_RETRY', 3),
        'sleeptime' => env('SATIM_HTTP_CLIENT_SLEEPTIME', 300),
    ],
];
