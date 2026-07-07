<?php

declare(strict_types=1);

namespace LaravelSatim;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Support\ServiceProvider;
use LaravelSatim\Client\SatimHttpClient;
use LaravelSatim\Client\SatimRetryMiddleware;
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\Support\SatimConfig;
use LaravelSatim\Validation\SatimValidator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class SatimServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'satim');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/satim.php' => config_path('satim.php')], 'satim-config');
            $this->publishes([__DIR__ . '/../lang' => $this->app->langPath('vendor/satim')], 'satim-lang');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/satim.php', 'satim');

        $this->app->singleton(SatimConfig::class, static function (): SatimConfig {
            /** @var array<string, mixed> $config */
            $config = is_array($raw = config('satim', [])) ? $raw : [];

            return new SatimConfig($config);
        });

        $this->app->bind(SatimHttpClientInterface::class, function (Application $app): SatimHttpClient {
            $factory = new HttpFactory();
            $config = $app->make(SatimConfig::class);

            return new SatimHttpClient(
                client: new GuzzleClient($this->guzzleOptions($config)),
                requestFactory: $factory,
                streamFactory: $factory,
                config: $config,
            );
        });

        $this->app->bind(
            SatimValidatorInterface::class,
            static fn (Application $app): SatimValidator => new SatimValidator($app->make(ValidatorFactory::class)),
        );

        $this->app->singleton(SatimGatewayInterface::class, static fn (Application $app): SatimGateway => new SatimGateway(
            $app->make(SatimHttpClientInterface::class),
            $app->make(SatimValidatorInterface::class),
        ));

        $this->app->alias(SatimGatewayInterface::class, 'laravel-satim');
    }

    /**
     * Build the Guzzle options, pushing a retry handler when retries are enabled.
     *
     * @return array<string, mixed>
     */
    private function guzzleOptions(SatimConfig $config): array
    {
        $options = $config->guzzleOptions();

        if ($config->retries() > 0) {
            $middleware = new SatimRetryMiddleware($config->retries(), $config->retryDelay());

            $stack = HandlerStack::create();
            $stack->push(Middleware::retry(
                static fn (
                    int $retries,
                    RequestInterface $request,
                    ?ResponseInterface $response = null,
                    ?Throwable $exception = null
                ): bool => $middleware->shouldRetry($retries, $request, $response, $exception),
                static fn (
                    int $retries
                ): int => $middleware->delay($retries),
            ));

            $options['handler'] = $stack;
        }

        return $options;
    }
}
