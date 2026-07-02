<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Satim;
use LaravelSatim\SatimServiceProvider;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('is registered by the application', function () {
    expect($this->app->getLoadedProviders())->toHaveKey(SatimServiceProvider::class);
});

it('binds the Satim contract to its concrete implementation', function () {
    expect($this->app->bound(SatimInterface::class))->toBeTrue()
        ->and(app(SatimInterface::class))->toBeInstanceOf(Satim::class);
});

it('registers the Satim contract as a shared singleton', function () {
    expect($this->app->isShared(SatimInterface::class))->toBeTrue()
        ->and(app(SatimInterface::class))->toBe(app(SatimInterface::class));
});

it('aliases "laravel-satim" to the Satim contract as the same instance', function () {
    expect($this->app->isAlias('laravel-satim'))->toBeTrue()
        ->and($this->app->getAlias('laravel-satim'))->toBe(SatimInterface::class)
        ->and(app('laravel-satim'))->toBe(app(SatimInterface::class));
});

it('merges the package configuration defaults while preserving host values', function () {
    config()->set('satim', ['username' => 'host_username']);

    (new SatimServiceProvider($this->app))->register();

    expect(config('satim.username'))->toBe('host_username')
        ->and(config('satim.api_url'))->toBe('https://test2.satim.dz/payment/rest')
        ->and(config('satim.http_client.method'))->toBe('POST')
        ->and(config('satim.http_client.retry'))->not->toBeNull()
        ->and(config('satim.http_options.timeout'))->not->toBeNull();
});

it('publishes the configuration file under the "config" tag', function () {
    $paths = ServiceProvider::pathsToPublish(SatimServiceProvider::class, 'config');

    expect($paths)->toBeArray()->not->toBeEmpty()
        ->and(array_values($paths))->toContain(config_path('satim.php'));

    $source = array_key_first($paths);

    expect($source)->toEndWith('satim.php')
        ->and(is_file($source))->toBeTrue();
});

it('ships a config file exposing every documented option with sane defaults', function () {
    $config = require dirname(__DIR__, 2).'/config/satim.php';

    expect($config)
        ->toBeArray()
        ->toHaveKeys([
            'api_url', 'username', 'password', 'terminal',
            'language', 'currency', 'http_options', 'http_client',
        ])
        ->and($config['api_url'])->toBe('https://test2.satim.dz/payment/rest')
        ->and($config['http_options'])->toHaveKeys(['verify', 'allow_redirects', 'timeout'])
        ->and($config['http_client'])->toHaveKeys(['method', 'retry', 'sleeptime'])
        ->and($config['http_client']['method'])->toBe('POST');
});
