<?php

declare(strict_types=1);

use LaravelSatim\Enums\Environment;

it('exposes the expected environment values', function (): void {
    expect(Environment::TESTING->value)->toBe('testing')
        ->and(Environment::STAGING->value)->toBe('staging')
        ->and(Environment::PRODUCTION->value)->toBe('production');
});

it('resolves environments from their string value', function (): void {
    expect(Environment::tryFrom('production'))->toBe(Environment::PRODUCTION)
        ->and(Environment::tryFrom('unknown'))->toBeNull();
});
