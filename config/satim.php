<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | Base URL of the SATIM REST API. It defaults to the test sandbox; switch
    | to the production URL provided by your bank when going live.
    |
    */

    'api_url' => env('SATIM_API_URL', 'https://test2.satim.dz/payment/rest'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | The username, password and terminal identifier issued to your merchant
    | account. Request them from your bank (CIB): https://cibweb.dz
    |
    */

    'username' => env('SATIM_USERNAME'),
    'password' => env('SATIM_PASSWORD'),
    'terminal' => env('SATIM_TERMINAL'),

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Default language and currency applied when a request does not specify
    | them explicitly.
    |
    | - language: 'AR', 'EN' or 'FR' (ISO 639-1). See the SatimLanguage enum.
    | - currency: only 'DZD' (ISO 4217 code 012) is currently supported by
    |   SATIM. See the SatimCurrency enum.
    |
    */

    'language' => env('SATIM_LANGUAGE', 'EN'),
    'currency' => env('SATIM_CURRENCY', 'DZD'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Transport Options
    |--------------------------------------------------------------------------
    |
    | Options forwarded to Laravel's HTTP client (Guzzle) for every request.
    |
    | - timeout: request timeout, in seconds.
    | - verify: TLS certificate verification. Disable only in local development.
    | - allow_redirects: follow HTTP redirects automatically.
    |
    */

    'http_options' => [
        'timeout' => env('SATIM_HTTP_TIMEOUT', 30),
        'verify' => env('SATIM_HTTP_VERIFY_SSL', true),
        'allow_redirects' => env('SATIM_HTTP_ALLOW_REDIRECTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Behaviour
    |--------------------------------------------------------------------------
    |
    | - method: HTTP verb used for API calls. SATIM strongly recommends 'POST'
    |   so sensitive data is never exposed in URLs, logs or browser history.
    |   Use 'GET' only if explicitly required.
    | - retry: number of retry attempts for transient network failures.
    | - sleeptime: delay between retries, in milliseconds.
    |
    */

    'http_client' => [
        'method' => env('SATIM_HTTP_CLIENT_METHOD', 'POST'),
        'retry' => env('SATIM_HTTP_CLIENT_RETRY', 3),
        'sleeptime' => env('SATIM_HTTP_CLIENT_SLEEPTIME', 300),
    ],

];
