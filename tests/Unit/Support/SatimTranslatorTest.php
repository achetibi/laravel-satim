<?php

declare(strict_types=1);

use LaravelSatim\Support\SatimTranslator;

it('lists the locales shipped by the package', function (): void {
    expect(SatimTranslator::supported())->toEqualCanonicalizing(['ar', 'en', 'fr']);
});

it('uses the active locale when it is supported', function (): void {
    app()->setLocale('ar');

    expect(SatimTranslator::locale())->toBe('ar');
});

it('falls back to the package default locale when the active locale is unsupported', function (): void {
    app()->setLocale('es');
    config()->set('satim.defaults.language', 'fr');

    expect(SatimTranslator::locale())->toBe('fr');
});

it('falls back to english when neither the active nor the default locale is supported', function (): void {
    app()->setLocale('es');
    config()->set('satim.defaults.language', 'de');

    expect(SatimTranslator::locale())->toBe('en');
});

it('translates a package key using the resolved locale', function (): void {
    app()->setLocale('en');

    expect(SatimTranslator::get('satim::exceptions.malformed_response'))
        ->toBe('An invalid response was received from the gateway.');
});

it('translates package keys through the fallback locale for an unsupported locale', function (): void {
    app()->setLocale('es');
    config()->set('satim.defaults.language', 'en');

    expect(SatimTranslator::get('satim::exceptions.malformed_response'))
        ->toBe('An invalid response was received from the gateway.');
});

it('applies replacements', function (): void {
    app()->setLocale('en');

    expect(SatimTranslator::get('satim::exceptions.http_error', ['status' => 502, 'reason' => 'Bad Gateway']))
        ->toContain('502')
        ->toContain('Bad Gateway');
});

it('returns the key when the translation line is missing', function (): void {
    expect(SatimTranslator::get('satim::exceptions.does_not_exist'))
        ->toBe('satim::exceptions.does_not_exist');
});

it('reports whether a key exists for the resolved locale', function (): void {
    expect(SatimTranslator::has('satim::exceptions.malformed_response'))->toBeTrue()
        ->and(SatimTranslator::has('satim::exceptions.nope'))->toBeFalse();
});

it('returns a group of translation lines as an array', function (): void {
    app()->setLocale('en');

    expect(SatimTranslator::group('satim::validation'))
        ->toBeArray()
        ->toHaveKey('required');
});
