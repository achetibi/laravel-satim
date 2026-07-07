<?php

declare(strict_types=1);

use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Responses\SatimRefundResponse;

it('builds the refund payload from the request amount, not a hardcoded value', function (): void {
    $payload = (new SatimRefundRequest(orderId: 'ORD123', amount: 1500.00))->payload();

    expect($payload)->toBe([
        'orderId' => 'ORD123',
        'amount' => 150000,
    ]);
});

it('exposes validation rules', function (): void {
    expect((new SatimRefundRequest(orderId: 'ORD123', amount: 50.00))->rules())
        ->toHaveKeys(['orderId', 'amount']);
});

it('maps a psr response to a refund response', function (): void {
    $response = (new SatimRefundRequest(orderId: 'ORD123', amount: 50.00))
        ->toResponse(jsonResponse(['errorCode' => '0']));

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->successful())->toBeTrue();
});
