<?php

declare(strict_types=1);

namespace LaravelSatim;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Support\ServiceProvider;
use LaravelSatim\Client\SatimHttpClient;
use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\Support\SatimConfig;
use LaravelSatim\Validation\SatimValidator;

class SatimServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'satim');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/satim.php' => config_path('satim.php')], 'satim-config');
            $this->publishes([__DIR__.'/../lang' => $this->app->langPath('vendor/satim')], 'satim-lang');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/satim.php', 'satim');

        $this->app->singleton(SatimConfig::class, fn ($app) => new SatimConfig($app['config']->get('satim')));

        $this->app->bind(SatimHttpClientInterface::class, function ($app) {
            $factory = new HttpFactory();
            $config = $app->make(SatimConfig::class);

            return new SatimHttpClient(
                client: new GuzzleClient($config->guzzleOptions()),
                requestFactory: $factory,
                streamFactory: $factory,
                config: $config,
            );
        });

        $this->app->bind(
            SatimValidatorInterface::class,
            fn ($app) => new SatimValidator($app->make(ValidatorFactory::class)),
        );

        $this->app->singleton(SatimGatewayInterface::class, fn ($app) => new SatimGateway(
            $app->make(SatimHttpClientInterface::class),
            $app->make(SatimValidatorInterface::class),
        ));

        $this->app->alias(SatimGatewayInterface::class, 'laravel-satim');
    }
}
