<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use LaravelSatim\Client\SatimRetryMiddleware;

$request = new Request('POST', 'https://test2.satim.dz/payment/rest/register.do');

it('stops retrying once the maximum is reached', function () use ($request): void {
    $middleware = new SatimRetryMiddleware(maxRetries: 2, delayMs: 100);

    expect($middleware->shouldRetry(2, $request))->toBeFalse();
});

it('retries on a connection exception', function () use ($request): void {
    $middleware = new SatimRetryMiddleware(maxRetries: 2, delayMs: 100);
    $exception = new ConnectException('timed out', $request);

    expect($middleware->shouldRetry(0, $request, null, $exception))->toBeTrue();
});

it('retries on a 5xx response but not on a 4xx response', function () use ($request): void {
    $middleware = new SatimRetryMiddleware(maxRetries: 2, delayMs: 100);

    expect($middleware->shouldRetry(0, $request, jsonResponse([], 503)))->toBeTrue()
        ->and($middleware->shouldRetry(0, $request, jsonResponse([], 404)))->toBeFalse();
});

it('does not retry a successful response', function () use ($request): void {
    $middleware = new SatimRetryMiddleware(maxRetries: 2, delayMs: 100);

    expect($middleware->shouldRetry(0, $request, jsonResponse([], 200)))->toBeFalse();
});

it('applies a linear back-off delay', function (): void {
    $middleware = new SatimRetryMiddleware(maxRetries: 3, delayMs: 250);

    expect($middleware->delay(1))->toBe(250)
        ->and($middleware->delay(3))->toBe(750);
});
