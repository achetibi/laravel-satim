<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimCurrency;

it('exposes the DZD currency with its ISO 4217 numeric code', function () {
    expect(SatimCurrency::DZD->value)->toBe('012');
});

it('falls back to DZD', function () {
    expect(SatimCurrency::fallback())->toBe(SatimCurrency::DZD);
});
