<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Environment;
use LaravelSatim\Enums\HttpMethod;
use LaravelSatim\Enums\Language;

return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The SATIM environment the package talks to. It selects which entry of the
    | "base_urls" array below is used. Supported values are backed by the
    | Environment enum: "test", "staging" and "prod".
    |
    */

    'environment' => env('SATIM_ENV', Environment::TEST->value),

    /*
    |--------------------------------------------------------------------------
    | Merchant credentials
    |--------------------------------------------------------------------------
    |
    | Credentials issued by SATIM for your merchant account. "username" and
    | "password" are mandatory and are injected into every request by the HTTP
    | client. "terminal_id" is optional; when set it is forwarded as
    | "force_terminal_id" inside the registration "jsonParams".
    |
    */

    'credentials' => [
        'username' => env('SATIM_USERNAME'),
        'password' => env('SATIM_PASSWORD'),
        'terminal_id' => env('SATIM_TERMINAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    |
    | The gateway base URL used for each environment. The active URL is resolved
    | from the "environment" value above. Trailing slashes are trimmed
    | automatically before endpoints are appended.
    |
    */

    'base_urls' => [
        Environment::TEST->value => env('SATIM_URL_TEST', 'https://test2.satim.dz/payment/rest'),
        Environment::STAGING->value => env('SATIM_URL_STAGING', 'https://test.satim.dz/payment/rest'),
        Environment::PRODUCTION->value => env('SATIM_URL_PROD', 'https://cib.satim.dz/payment/rest'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP client
    |--------------------------------------------------------------------------
    |
    | Low-level transport settings for the underlying Guzzle client.
    |
    | "method"        The HTTP verb used for every gateway call. SATIM strongly
    |                 recommends POST so that credentials travel in the request
    |                 body rather than being exposed in the URL, query string,
    |                 proxy logs or browser history. Supported values are backed
    |                 by the HttpMethod enum: "POST" (recommended) and "GET".
    | "timeout"       Maximum total duration, in seconds, of a request.
    | "connect_timeout" Maximum duration, in seconds, to establish a connection.
    | "retries"       Number of automatic retries for transport failures and 5xx
    |                 responses. Set to 0 to disable retrying.
    | "retry_delay"   Base linear back-off delay, in milliseconds, between retries.
    | "verify"        TLS certificate verification. Disable in local development
    |                 only, never in production.
    | "options"       Extra Guzzle request options merged as-is (advanced use).
    |
    */

    'http' => [
        'method' => env('SATIM_HTTP_METHOD', HttpMethod::POST->value),
        'timeout' => (int) env('SATIM_TIMEOUT', 30),
        'connect_timeout' => (int) env('SATIM_CONNECT_TIMEOUT', 10),
        'retries' => (int) env('SATIM_RETRIES', 2),
        'retry_delay' => (int) env('SATIM_RETRY_DELAY', 300),
        'verify' => env('SATIM_SSL_VERIFY', true),
        'options' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Fallback currency and language applied when a request does not specify
    | them. The "language" value is also the package translation fallback: when
    | the active Laravel locale is not shipped by the package (ar, en, fr), the
    | package texts are resolved against this locale instead. Values are backed
    | by the Currency and Language enums.
    |
    */

    'defaults' => [
        'currency' => env('SATIM_CURRENCY', Currency::DZD->value),
        'language' => env('SATIM_LANGUAGE', Language::ENGLISH->value),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, the HTTP client logs each gateway interaction (endpoint,
    | method and response status) to the given channel. Sensitive payload data
    | such as credentials is never logged.
    |
    */

    'logging' => [
        'enabled' => (bool) env('SATIM_LOG', false),
        'channel' => env('SATIM_LOG_CHANNEL', 'stack'),
    ],

];
