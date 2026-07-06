<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Environment;
use LaravelSatim\Enums\Language;

return [

    'environment' => env('SATIM_ENV', Environment::TEST->value),

    'credentials' => [
        'username' => env('SATIM_USERNAME'),
        'password' => env('SATIM_PASSWORD'),
        'terminal_id' => env('SATIM_TERMINAL'),
    ],

    'base_urls' => [
        Environment::TEST->value => env('SATIM_URL_TEST', 'https://test2.satim.dz/payment/rest'),
        Environment::STAGING->value => env('SATIM_URL_STAGING', 'https://test.satim.dz/payment/rest'),
        Environment::PRODUCTION->value => env('SATIM_URL_PROD', 'https://cib.satim.dz/payment/rest'),
    ],

    'http' => [
        'client' => null,
        'timeout' => (int) env('SATIM_TIMEOUT', 30),
        'connect_timeout' => (int) env('SATIM_CONNECT_TIMEOUT', 10),
        'retries' => (int) env('SATIM_RETRIES', 2),
        'verify' => env('SATIM_SSL_VERIFY', true),
        'options' => [],
    ],

    'defaults' => [
        'currency' => Currency::DZD->value,
        'language' => Language::FRENCH->value,
    ],

    'logging' => [
        'enabled' => (bool) env('SATIM_LOG', false),
        'channel' => env('SATIM_LOG_CHANNEL', 'stack'),
    ],

];
