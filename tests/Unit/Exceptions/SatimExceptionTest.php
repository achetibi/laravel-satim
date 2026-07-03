<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimAuthenticationException;
use LaravelSatim\Exceptions\SatimConfigurationException;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Exceptions\SatimException;
use LaravelSatim\Exceptions\SatimPaymentException;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Exceptions\SatimValidationException;

it('carries the SATIM error code and raw context', function () {
    $exception = new SatimPaymentException('Declined', 6, ['ErrorCode' => 6]);

    expect($exception->getMessage())->toBe('Declined')
        ->and($exception->errorCode())->toBe(6)
        ->and($exception->context())->toBe(['ErrorCode' => 6]);
});

it('defaults the error code to zero and the context to an empty array', function () {
    $exception = new SatimResponseException('oops');

    expect($exception->errorCode())->toBe(0)
        ->and($exception->context())->toBe([]);
});

it('preserves a previous throwable', function () {
    $previous = new RuntimeException('root cause');
    $exception = new SatimConnectionException('failed', 0, [], $previous);

    expect($exception->getPrevious())->toBe($previous);
});

it('exposes a coherent exception hierarchy', function (string $class, string $parent) {
    expect(is_subclass_of($class, $parent))->toBeTrue();
})->with([
    'configuration' => [SatimConfigurationException::class, SatimException::class],
    'validation' => [SatimValidationException::class, SatimException::class],
    'connection' => [SatimConnectionException::class, SatimException::class],
    'response' => [SatimResponseException::class, SatimException::class],
    'authentication' => [SatimAuthenticationException::class, SatimResponseException::class],
    'payment' => [SatimPaymentException::class, SatimResponseException::class],
]);
