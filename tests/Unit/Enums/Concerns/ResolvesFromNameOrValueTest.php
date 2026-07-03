<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;

it('resolves a case from its backing value', function () {
    expect(SatimCurrency::resolve('012'))->toBe(SatimCurrency::DZD);
});

it('resolves a case from its name', function () {
    expect(SatimCurrency::resolve('DZD'))->toBe(SatimCurrency::DZD);
});

it('is case-insensitive on both value and name', function () {
    expect(SatimCurrency::resolve('dzd'))->toBe(SatimCurrency::DZD)
        ->and(SatimLanguage::resolve('fr'))->toBe(SatimLanguage::FR)
        ->and(SatimLanguage::resolve('Ar'))->toBe(SatimLanguage::AR);
});

it('returns null for null, empty or unknown values', function () {
    expect(SatimCurrency::resolve(null))->toBeNull()
        ->and(SatimCurrency::resolve(''))->toBeNull()
        ->and(SatimCurrency::resolve('EUR'))->toBeNull()
        ->and(SatimLanguage::resolve('zz'))->toBeNull();
});
