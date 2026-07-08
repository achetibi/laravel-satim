<?php

declare(strict_types=1);

use LaravelSatim\Enums\Language;
use LaravelSatim\Http\Requests\SatimStatusRequest;
use LaravelSatim\Http\Responses\SatimStatusResponse;

it('builds the status payload', function (): void {
    $payload = (new SatimStatusRequest(
        orderId: 'ehf9z2yvvThwQ4AACW2G',
        language: Language::FRENCH,
    ))->payload();

    expect($payload)->toBe([
        'orderId' => 'ehf9z2yvvThwQ4AACW2G',
        'language' => 'fr',
    ]);
});

it('exposes validation rules', function (): void {
    expect((new SatimStatusRequest(orderId: 'ORD123'))->rules())
        ->toHaveKeys(['orderId', 'language']);
});

it('maps a psr response to a status response', function (): void {
    $response = (new SatimStatusRequest(orderId: 'ehf9z2yvvThwQ4AACW2G'))->toResponse(jsonResponse([
        'errorCode' => '0',
        'orderStatus' => 2,
    ]));

    expect($response)->toBeInstanceOf(SatimStatusResponse::class)
        ->and($response->successful())->toBeTrue();
});
