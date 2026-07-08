<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Environment;
use LaravelSatim\Enums\HttpMethod;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimConfigurationException;

it('resolves the environment', function (): void {
    expect(satimConfig()->environment())->toBe(Environment::TESTING);
});

it('throws on an invalid environment', function (): void {
    satimConfig(['environment' => 'nope'])->environment();
})->throws(SatimConfigurationException::class);

it('trims the trailing slash from the base url', function (): void {
    expect(satimConfig()->baseUrl())->toBe('https://test2.satim.dz/payment/rest');
});

it('throws when the base url is missing', function (): void {
    satimConfig(['base_urls' => ['testing' => null]])->baseUrl();
})->throws(SatimConfigurationException::class);

it('returns the gateway credentials', function (): void {
    expect(satimConfig()->credentials())->toBe([
        'userName' => 'user',
        'password' => 'pass',
    ]);
});

it('throws when a credential is missing', function (): void {
    satimConfig(['credentials' => ['username' => null]])->credentials();
})->throws(SatimConfigurationException::class);

it('returns the terminal id when present', function (): void {
    expect(satimConfig()->terminalId())->toBe('terminal');
});

it('returns null when the terminal id is absent', function (): void {
    expect(satimConfig(['credentials' => ['terminal_id' => null]])->terminalId())->toBeNull();
});

it('defaults the http method to POST', function (): void {
    expect(satimConfig(['http' => ['method' => null]])->httpMethod())->toBe(HttpMethod::POST);
});

it('reads the configured http method case-insensitively', function (): void {
    expect(satimConfig(['http' => ['method' => 'get']])->httpMethod())->toBe(HttpMethod::GET);
});

it('throws on an invalid http method', function (): void {
    satimConfig(['http' => ['method' => 'PATCH']])->httpMethod();
})->throws(SatimConfigurationException::class);

it('never returns negative retries', function (): void {
    expect(satimConfig(['http' => ['retries' => -5]])->retries())->toBe(0)
        ->and(satimConfig(['http' => ['retries' => 3]])->retries())->toBe(3);
});

it('exposes the retry delay', function (): void {
    expect(satimConfig(['http' => ['retry_delay' => 500]])->retryDelay())->toBe(500);
});

it('merges extra guzzle options while forcing http_errors off', function (): void {
    $options = satimConfig()->guzzleOptions();

    expect($options['http_errors'])->toBeFalse()
        ->and($options['timeout'])->toBe(30.0)
        ->and($options['connect_timeout'])->toBe(10.0)
        ->and($options['verify'])->toBeTrue()
        ->and($options['allow_redirects'])->toBeFalse();
});

it('resolves the default currency and language', function (): void {
    expect(satimConfig()->defaultCurrency())->toBe(Currency::DZD)
        ->and(satimConfig()->defaultLanguage())->toBe(Language::FRENCH);
});

it('throws on an invalid default currency', function (): void {
    satimConfig(['defaults' => ['currency' => 'USD']])->defaultCurrency();
})->throws(SatimConfigurationException::class);

it('exposes logging settings', function (): void {
    expect(satimConfig(['logging' => ['enabled' => true]])->loggingEnabled())->toBeTrue()
        ->and(satimConfig()->logChannel())->toBe('satim');
});
