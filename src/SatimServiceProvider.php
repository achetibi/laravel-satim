<?php

declare(strict_types=1);

namespace LaravelSatim;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimConfigurationException;
use LaravelSatim\Http\SatimErrorHandler;
use LaravelSatim\Http\SatimHttpClient;
use LaravelSatim\Support\SatimCredentials;

class SatimServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/satim.php' => config_path('satim.php')], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/satim.php', 'satim');

        $this->app->singleton(SatimHttpClient::class, fn (Application $app): SatimHttpClient => $this->makeHttpClient($app));
        $this->app->singleton(SatimErrorHandler::class);
        $this->app->singleton(SatimInterface::class, Satim::class);
        $this->app->alias(SatimInterface::class, 'laravel-satim');
    }

    /**
     * @throws SatimConfigurationException
     */
    private function makeHttpClient(Application $app): SatimHttpClient
    {
        /** @var Repository $config */
        $config = $app->make(Repository::class);

        $baseUrl = $config->get('satim.api_url');

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw new SatimConfigurationException('SATIM API URL is not configured.');
        }

        return new SatimHttpClient(
            credentials: SatimCredentials::fromConfig(
                $config->get('satim.username'),
                $config->get('satim.password'),
                $config->get('satim.terminal'),
            ),
            baseUrl: $baseUrl,
            defaultCurrency: SatimCurrency::resolve($this->stringConfig($config, 'satim.currency')) ?? SatimCurrency::fallback(),
            defaultLanguage: SatimLanguage::resolve($this->stringConfig($config, 'satim.language')) ?? SatimLanguage::fallback(),
            method: $this->stringConfig($config, 'satim.http_client.method') ?? 'POST',
            retry: $this->intConfig($config, 'satim.http_client.retry', 0),
            retryDelay: $this->intConfig($config, 'satim.http_client.sleeptime', 1),
            httpOptions: [
                'verify' => $config->get('satim.http_options.verify', true),
                'allow_redirects' => $config->get('satim.http_options.allow_redirects', false),
                'timeout' => $this->intConfig($config, 'satim.http_options.timeout', 30),
            ],
        );
    }

    private function stringConfig(Repository $config, string $key): ?string
    {
        $value = $config->get($key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function intConfig(Repository $config, string $key, int $default): int
    {
        $value = $config->get($key);

        return is_numeric($value) ? (int) $value : $default;
    }
}
