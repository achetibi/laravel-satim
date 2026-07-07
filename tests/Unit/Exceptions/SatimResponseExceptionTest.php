<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimResponseException;

beforeEach(fn () => app()->setLocale('en'));

it('maps a known gateway error code to its translated message', function (): void {
    $exception = SatimResponseException::fromCode('1');

    expect($exception->errorCode)->toBe('1')
        ->and($exception->getMessage())->toBe('The order number has already been used.');
});

it('uses the provided error message when the code is unknown', function (): void {
    $exception = SatimResponseException::fromCode('999', 'Gateway said no');

    expect($exception->errorCode)->toBe('999')
        ->and($exception->errorMessage)->toBe('Gateway said no')
        ->and($exception->getMessage())->toBe('Gateway said no');
});

it('falls back to the unknown message when no code and no message match', function (): void {
    $exception = SatimResponseException::fromCode('999');

    expect($exception->errorMessage)->toBe('unknown')
        ->and($exception->getMessage())->toBe('An unexpected error occurred.');
});

it('builds an http error with status and reason', function (): void {
    $exception = SatimResponseException::httpError(502, 'Bad Gateway');

    expect($exception->errorCode)->toBe('502')
        ->and($exception->getMessage())->toContain('502')->toContain('Bad Gateway');
});

it('builds a malformed-response exception', function (): void {
    expect(SatimResponseException::malformed()->getMessage())
        ->toBe('An invalid response was received from the gateway.');
});
