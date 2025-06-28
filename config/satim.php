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
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout used in the SATIM API requests
    |
    */
    'timeout' => env('SATIM_TIMEOUT', 30),
];
