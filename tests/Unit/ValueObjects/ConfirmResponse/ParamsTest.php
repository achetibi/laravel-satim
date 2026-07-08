<?php

declare(strict_types=1);

use LaravelSatim\ValueObjects\ConfirmResponse\Params;

it('maps the known confirmation params', function (): void {
    $params = Params::fromArray([
        'respCode_desc' => 'Votre paiement a été accepté.',
        'udf1' => 'RES202600008',
        'respCode' => '00',
    ]);

    expect($params->respCode)->toBe('00')
        ->and($params->respCodeDesc)->toBe('Votre paiement a été accepté.')
        ->and($params->udf1)->toBe('RES202600008')
        ->and($params->udf2)->toBeNull()
        ->and($params->extra)->toBe([]);
});

it('keeps unmodelled params in extra', function (): void {
    $params = Params::fromArray([
        'respCode' => '00',
        'udf2' => 'B',
        'somethingElse' => 'kept',
    ]);

    expect($params->respCode)->toBe('00')
        ->and($params->udf2)->toBe('B')
        ->and($params->respCodeDesc)->toBeNull()
        ->and($params->extra)->toBe(['somethingElse' => 'kept']);
});

it('defaults every field to null when built from an empty array', function (): void {
    $params = Params::fromArray([]);

    expect($params->respCode)->toBeNull()
        ->and($params->respCodeDesc)->toBeNull()
        ->and($params->udf1)->toBeNull()
        ->and($params->extra)->toBe([]);
});
