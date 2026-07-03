<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

it('parses the register response fields', function () {
    $response = SatimRegisterResponse::fromResponse([
        'errorCode' => '0',
        'orderId' => 'QNA1IhS444MlTAAAGN6I',
        'formUrl' => 'https://test2.satim.dz/payment/...',
    ]);

    expect($response)->toBeInstanceOf(SatimResponseInterface::class)
        ->and($response->orderId)->toBe('QNA1IhS444MlTAAAGN6I')
        ->and($response->formUrl)->toBe('https://test2.satim.dz/payment/...');
});

it('handles an empty response gracefully', function () {
    $response = SatimRegisterResponse::fromResponse(null);

    expect($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull();
});
