<?php

declare(strict_types=1);

use LaravelSatim\Enums\Language;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;

it('builds the confirmation payload', function (): void {
    $payload = (new SatimConfirmRequest(
        mdOrder: 'BnTjnFDzZSP97QXu8FXq',
        language: Language::FRENCH,
    ))->payload();

    expect($payload)->toBe([
        'mdOrder' => 'BnTjnFDzZSP97QXu8FXq',
        'language' => 'fr',
    ]);
});

it('exposes validation rules', function (): void {
    expect((new SatimConfirmRequest(mdOrder: 'x'))->rules())
        ->toHaveKeys(['mdOrder', 'language']);
});

it('maps a psr response to a confirm response', function (): void {
    $response = (new SatimConfirmRequest(mdOrder: 'BnTjnFDzZSP97QXu8FXq'))->toResponse(jsonResponse([
        'errorCode' => '0',
        'OrderStatus' => 2,
    ]));

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeTrue();
});
