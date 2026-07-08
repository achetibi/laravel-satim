<?php

declare(strict_types=1);

use LaravelSatim\ValueObjects\StatusResponse\MerchantOrderParams;

it('maps and types the known merchant order params', function (): void {
    $params = MerchantOrderParams::fromArray([
        'disablePhone' => 'false',
        'disableEmail' => 'true',
        'force_terminal_id' => 'CIB0099887',
        'udf1' => 'CMD-2026-0042',
        'udf2' => 'customer@example.dz',
        'transmissionDate' => '1735689600500',
    ]);

    expect($params->disablePhone)->toBeFalse()
        ->and($params->disableEmail)->toBeTrue()
        ->and($params->forceTerminalId)->toBe('CIB0099887')
        ->and($params->udf1)->toBe('CMD-2026-0042')
        ->and($params->udf2)->toBe('customer@example.dz')
        ->and($params->transmissionDate)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($params->transmissionDate?->format('Y-m-d\TH:i:s.vP'))->toBe('2025-01-01T00:00:00.500+00:00');
});

it('keeps unmodelled params in extra and defaults the rest to null', function (): void {
    $params = MerchantOrderParams::fromArray([
        'udf3' => 'REF-77',
        'phone' => '0600000000',
        'email' => 'buyer@example.dz',
    ]);

    expect($params->udf3)->toBe('REF-77')
        ->and($params->udf1)->toBeNull()
        ->and($params->disableEmail)->toBeNull()
        ->and($params->transmissionDate)->toBeNull()
        ->and($params->extra)->toBe([
            'phone' => '0600000000',
            'email' => 'buyer@example.dz',
        ]);
});

it('is empty when built from an empty array', function (): void {
    $params = MerchantOrderParams::fromArray([]);

    expect($params->disablePhone)->toBeNull()
        ->and($params->forceTerminalId)->toBeNull()
        ->and($params->extra)->toBe([]);
});
