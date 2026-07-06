<?php

namespace LaravelSatim\Tests;

use LaravelSatim\SatimServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('config:clear');
    }

    protected function getPackageProviders($app): array
    {
        return [SatimServiceProvider::class];
    }
}
