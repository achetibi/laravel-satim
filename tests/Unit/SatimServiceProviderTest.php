<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\SatimErrorHandler;
use LaravelSatim\Http\SatimHttpClient;
use LaravelSatim\Satim;
use LaravelSatim\SatimServiceProvider;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('is registered by the application', function () {
    expect($this->app->getLoadedProviders())->toHaveKey(SatimServiceProvider::class);
});

it('binds the Satim contract to its concrete implementation as a shared singleton', function () {
    expect($this->app->isShared(SatimInterface::class))->toBeTrue()
        ->and(app(SatimInterface::class))->toBeInstanceOf(Satim::class)
        ->and(app(SatimInterface::class))->toBe(app(SatimInterface::class));
});

it('aliases "laravel-satim" to the same instance as the contract', function () {
    expect($this->app->getAlias('laravel-satim'))->toBe(SatimInterface::class)
        ->and(app('laravel-satim'))->toBe(app(SatimInterface::class));
});

it('binds the http client with injected credentials and the error handler', function () {
    expect(app(SatimHttpClient::class))->toBeInstanceOf(SatimHttpClient::class)
        ->and(app(SatimHttpClient::class))->toBe(app(SatimHttpClient::class))
        ->and(app(SatimErrorHandler::class))->toBeInstanceOf(SatimErrorHandler::class);
});

it('merges the package configuration defaults while preserving host values', function () {
    config()->set('satim', ['username' => 'host_username']);

    (new SatimServiceProvider($this->app))->register();

    expect(config('satim.username'))->toBe('host_username')
        ->and(config('satim.api_url'))->toBe('https://test2.satim.dz/payment/rest')
        ->and(config('satim.http_client.method'))->toBe('POST');
});

it('publishes the configuration file under the "config" tag', function () {
    $paths = ServiceProvider::pathsToPublish(SatimServiceProvider::class, 'config');

    expect($paths)->toBeArray()->not->toBeEmpty()
        ->and(array_values($paths))->toContain(config_path('satim.php'));
});
