<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimAbstractException;
use LaravelSatim\Exceptions\SatimConfigurationException;

beforeEach(fn () => app()->setLocale('en'));

it('builds a missing-key exception with the key interpolated', function (): void {
    $exception = SatimConfigurationException::missing('credentials.username');

    expect($exception)->toBeInstanceOf(SatimAbstractException::class)
        ->and($exception->getMessage())->toContain('credentials.username');
});

it('builds an invalid-environment exception', function (): void {
    expect(SatimConfigurationException::invalidEnvironment('nope')->getMessage())
        ->toContain('nope');
});

it('builds a missing-base-url exception', function (): void {
    expect(SatimConfigurationException::missingBaseUrl('prod')->getMessage())
        ->toContain('prod');
});

it('builds an invalid-value exception', function (): void {
    $message = SatimConfigurationException::invalidValue('http.method', 'PATCH')->getMessage();

    expect($message)->toContain('http.method')->toContain('PATCH');
});
