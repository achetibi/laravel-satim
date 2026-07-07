<?php

declare(strict_types=1);

namespace LaravelSatim\Tests;

use LaravelSatim\SatimServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('satim.environment', 'test');
        $app['config']->set('satim.credentials', [
            'username' => 'test-user',
            'password' => 'test-pass',
            'terminal_id' => 'test-terminal',
        ]);
        $app['config']->set('satim.defaults.language', 'fr');
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [SatimServiceProvider::class];
    }
}
