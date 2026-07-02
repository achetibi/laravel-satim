<?php

declare(strict_types=1);

namespace LaravelSatim;

use Illuminate\Support\ServiceProvider;
use LaravelSatim\Contracts\SatimInterface;

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
        $this->registerServices();
    }

    protected function registerServices(): void
    {
        $this->app->singleton(SatimInterface::class, Satim::class);
        $this->app->alias(SatimInterface::class, 'laravel-satim');
    }
}
