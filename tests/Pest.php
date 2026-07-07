<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use LaravelSatim\Support\SatimConfig;
use LaravelSatim\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;

uses(TestCase::class)->in('Unit');

/**
 * Build a SatimConfig from a sensible baseline, recursively overridden.
 *
 * @param  array<string, mixed>  $overrides
 */
function satimConfig(array $overrides = []): SatimConfig
{
    return new SatimConfig(array_replace_recursive([
        'environment' => 'test',
        'credentials' => [
            'username' => 'user',
            'password' => 'pass',
            'terminal_id' => 'terminal',
        ],
        'base_urls' => [
            'test' => 'https://test2.satim.dz/payment/rest/',
        ],
        'http' => [
            'method' => 'POST',
            'timeout' => 30,
            'connect_timeout' => 10,
            'retries' => 2,
            'retry_delay' => 300,
            'verify' => true,
            'options' => ['allow_redirects' => false],
        ],
        'defaults' => [
            'currency' => 'DZD',
            'language' => 'fr',
        ],
        'logging' => [
            'enabled' => false,
            'channel' => 'satim',
        ],
    ], $overrides));
}

/**
 * Build a PSR-7 JSON response for use in HTTP-level tests.
 *
 * @param  array<string, mixed>  $body
 */
function jsonResponse(array $body, int $status = 200): ResponseInterface
{
    return new Response($status, ['Content-Type' => 'application/json'], (string) json_encode($body));
}

/**
 * Build a raw PSR-7 response with an arbitrary body.
 */
function rawResponse(string $body, int $status = 200): ResponseInterface
{
    return new Response($status, [], $body);
}
