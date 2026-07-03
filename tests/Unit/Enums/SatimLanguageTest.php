<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimLanguage;

it('exposes the supported ISO 639-1 languages', function () {
    expect(SatimLanguage::AR->value)->toBe('AR')
        ->and(SatimLanguage::EN->value)->toBe('EN')
        ->and(SatimLanguage::FR->value)->toBe('FR');
});

it('falls back to EN', function () {
    expect(SatimLanguage::fallback())->toBe(SatimLanguage::EN);
});
