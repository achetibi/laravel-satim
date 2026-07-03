<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Facades\Satim;
use LaravelSatim\Satim as SatimService;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('lives in the Facades namespace and extends the base facade', function () {
    expect(class_exists(Satim::class))->toBeTrue()
        ->and(is_subclass_of(Satim::class, Facade::class))->toBeTrue();
});

it('resolves to the Satim service and shares the container instance', function () {
    expect(Satim::getFacadeRoot())
        ->toBeInstanceOf(SatimService::class)
        ->toBe(app(SatimInterface::class));
});
