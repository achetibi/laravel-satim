<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;

it('resolves the currency by ISO value or by name, case-insensitively', function () {
    expect(SatimCurrency::resolve('012'))->toBe(SatimCurrency::DZD)
        ->and(SatimCurrency::resolve('DZD'))->toBe(SatimCurrency::DZD)
        ->and(SatimCurrency::resolve('dzd'))->toBe(SatimCurrency::DZD)
        ->and(SatimCurrency::resolve('Dzd'))->toBe(SatimCurrency::DZD)
        ->and(SatimCurrency::resolve('EUR'))->toBeNull()
        ->and(SatimCurrency::resolve(''))->toBeNull()
        ->and(SatimCurrency::resolve(null))->toBeNull();
});

it('resolves the language by value or by name, case-insensitively', function () {
    expect(SatimLanguage::resolve('EN'))->toBe(SatimLanguage::EN)
        ->and(SatimLanguage::resolve('en'))->toBe(SatimLanguage::EN)
        ->and(SatimLanguage::resolve('fr'))->toBe(SatimLanguage::FR)
        ->and(SatimLanguage::resolve('Ar'))->toBe(SatimLanguage::AR)
        ->and(SatimLanguage::resolve('AR'))->toBe(SatimLanguage::AR)
        ->and(SatimLanguage::resolve('zz'))->toBeNull()
        ->and(SatimLanguage::resolve(null))->toBeNull();
});

it('falls back to sensible defaults', function () {
    expect(SatimCurrency::fallback())->toBe(SatimCurrency::DZD)
        ->and(SatimLanguage::fallback())->toBe(SatimLanguage::EN);
});
