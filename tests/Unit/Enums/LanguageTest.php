<?php

declare(strict_types=1);

use LaravelSatim\Enums\Language;

it('maps each language to its gateway code', function (): void {
    expect(Language::ARABIC->code())->toBe('ar')
        ->and(Language::ENGLISH->code())->toBe('en')
        ->and(Language::FRENCH->code())->toBe('fr');
});

it('returns the given language from withFallback when provided', function (): void {
    expect(Language::withFallback(Language::ARABIC))->toBe(Language::ARABIC);
});

it('returns the configured default from withFallback when null', function (): void {
    config()->set('satim.defaults.language', Language::FRENCH->value);

    expect(Language::withFallback(null))->toBe(Language::FRENCH);
});
