<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;

it('maps each currency to its numeric ISO code', function (): void {
    expect(Currency::DZD->code())->toBe('012');
});

it('resolves a currency from its numeric code', function (): void {
    expect(Currency::fromCode('012'))->toBe(Currency::DZD);
});

it('falls back to the configured default currency for an unknown code', function (): void {
    config()->set('satim.defaults.currency', Currency::DZD->value);

    expect(Currency::fromCode('999'))->toBe(Currency::DZD);
});

it('returns the given currency from withFallback when provided', function (): void {
    expect(Currency::withFallback(Currency::DZD))->toBe(Currency::DZD);
});

it('returns the configured default from withFallback when null', function (): void {
    config()->set('satim.defaults.currency', Currency::DZD->value);

    expect(Currency::withFallback(null))->toBe(Currency::DZD);
});
