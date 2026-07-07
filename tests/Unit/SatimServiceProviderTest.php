<?php

declare(strict_types=1);

use LaravelSatim\Client\SatimHttpClient;
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\SatimGateway;
use LaravelSatim\Support\SatimConfig;
use LaravelSatim\Validation\SatimValidator;

it('merges the package configuration', function (): void {
    expect(config('satim'))->toBeArray()
        ->and(config('satim.http.method'))->not->toBeNull();
});

it('binds the package contracts', function (): void {
    expect(app(SatimConfig::class))->toBeInstanceOf(SatimConfig::class)
        ->and(app(SatimHttpClientInterface::class))->toBeInstanceOf(SatimHttpClient::class)
        ->and(app(SatimValidatorInterface::class))->toBeInstanceOf(SatimValidator::class)
        ->and(app(SatimGatewayInterface::class))->toBeInstanceOf(SatimGateway::class);
});

it('exposes the gateway as a shared singleton and aliased binding', function (): void {
    expect(app(SatimGatewayInterface::class))->toBe(app(SatimGatewayInterface::class))
        ->and(app('laravel-satim'))->toBe(app(SatimGatewayInterface::class));
});

it('loads the package translations', function (): void {
    app()->setLocale('en');

    expect(trans('satim::exceptions.malformed_response'))
        ->toBe('An invalid response was received from the gateway.');
});

it('still builds the http client when retries are enabled', function (): void {
    config()->set('satim.http.retries', 3);
    app()->forgetInstance(SatimHttpClientInterface::class);

    expect(app(SatimHttpClientInterface::class))->toBeInstanceOf(SatimHttpClient::class);
});
