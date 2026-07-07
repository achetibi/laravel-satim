<?php

declare(strict_types=1);

use LaravelSatim\Enums\Environment;

it('exposes the expected environment values', function (): void {
    expect(Environment::TEST->value)->toBe('test')
        ->and(Environment::STAGING->value)->toBe('staging')
        ->and(Environment::PRODUCTION->value)->toBe('prod');
});

it('resolves environments from their string value', function (): void {
    expect(Environment::tryFrom('prod'))->toBe(Environment::PRODUCTION)
        ->and(Environment::tryFrom('unknown'))->toBeNull();
});
