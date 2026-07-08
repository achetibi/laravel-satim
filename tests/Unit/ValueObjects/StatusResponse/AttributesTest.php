<?php

declare(strict_types=1);

use LaravelSatim\ValueObjects\StatusResponse\Attributes;

it('maps the mdOrder attribute and keeps the rest in extra', function (): void {
    $attributes = Attributes::fromArray([
        'mdOrder' => 'aZ12Bc34Dd56Ef78Gh90',
        'clientId' => 'CL-778',
        'channel' => 'web',
    ]);

    expect($attributes->mdOrder)->toBe('aZ12Bc34Dd56Ef78Gh90')
        ->and($attributes->extra)->toBe([
            'clientId' => 'CL-778',
            'channel' => 'web',
        ]);
});

it('defaults to null when built from an empty array', function (): void {
    $attributes = Attributes::fromArray([]);

    expect($attributes->mdOrder)->toBeNull()
        ->and($attributes->extra)->toBe([]);
});
