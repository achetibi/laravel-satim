<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Satim;
use LaravelSatim\SatimFacade;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('check that the SatimFacade class exists', function () {
    expect(class_exists(SatimFacade::class))->toBeTrue();
});

it('check that the facade extends the base Facade class', function () {
    expect(is_subclass_of(SatimFacade::class, Facade::class))->toBeTrue();
});

it('check that the SatimFacade is resolvable and returns the correct underlying class', function () {
    expect(SatimFacade::getFacadeRoot())
        ->toBeInstanceOf(Satim::class)
        ->and(app(SatimInterface::class))->toBeInstanceOf(Satim::class);
});
