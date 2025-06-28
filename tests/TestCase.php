<?php

namespace LaravelSatim\Tests;

use LaravelSatim\SatimServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 27/06/2025
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('config:clear');
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 27/06/2025
     */
    protected function getPackageProviders($app): array
    {
        return [SatimServiceProvider::class];
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 27/06/2025
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('satim.username', 'test_username');
        $app['config']->set('satim.password', 'test_password');
        $app['config']->set('satim.terminal', 'test_terminal');
        $app['config']->set('satim.api_url', 'https://test.satim.dz/payment/rest');
        $app['config']->set('satim.timeout', 30);
        $app['config']->set('satim.language', 'en');
        $app['config']->set('satim.currency', 'DZD');
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 27/06/2025
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('satim', [
            'username' => 'test_username',
            'password' => 'test_password',
            'terminal' => 'test_terminal',
            'api_url' => 'https://test.satim.dz/payment/rest',
            'timeout' => 30,
            'language' => 'en',
            'currency' => 'DZD',
        ]);
    }
}
