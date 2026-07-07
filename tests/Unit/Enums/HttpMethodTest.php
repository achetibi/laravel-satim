<?php

declare(strict_types=1);

use LaravelSatim\Enums\HttpMethod;

it('exposes the supported HTTP methods', function (): void {
    expect(HttpMethod::POST->value)->toBe('POST')
        ->and(HttpMethod::GET->value)->toBe('GET');
});

it('resolves methods from their string value', function (): void {
    expect(HttpMethod::tryFrom('POST'))->toBe(HttpMethod::POST)
        ->and(HttpMethod::tryFrom('PATCH'))->toBeNull();
});
