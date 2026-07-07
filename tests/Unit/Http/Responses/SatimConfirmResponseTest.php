<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\OrderStatus;
use LaravelSatim\Http\Responses\SatimConfirmResponse;

it('is successful only when deposited with error code 0', function (): void {
    expect((new SatimConfirmResponse(['errorCode' => '0', 'OrderStatus' => 2]))->successful())->toBeTrue()
        ->and((new SatimConfirmResponse(['errorCode' => '0', 'OrderStatus' => 1]))->successful())->toBeFalse();
});

it('resolves the order status enum from a numeric value', function (): void {
    expect((new SatimConfirmResponse(['OrderStatus' => 2]))->orderStatus())->toBe(OrderStatus::DEPOSITED)
        ->and((new SatimConfirmResponse(['OrderStatus' => '3']))->orderStatus())->toBe(OrderStatus::REVERSED)
        ->and((new SatimConfirmResponse([]))->orderStatus())->toBeNull();
});

it('converts monetary amounts from centimes to major units', function (): void {
    $response = new SatimConfirmResponse(['Amount' => 150000, 'depositAmount' => 50000]);

    expect($response->amount())->toBe(1500.0)
        ->and($response->depositAmount())->toBe(500.0);
});

it('resolves the currency from its numeric code', function (): void {
    expect((new SatimConfirmResponse(['currency' => '012']))->currency())->toBe(Currency::DZD)
        ->and((new SatimConfirmResponse([]))->currency())->toBeNull();
});

it('reads nested response params', function (): void {
    $response = new SatimConfirmResponse([
        'params' => ['respCode' => '00', 'respCode_desc' => 'Approved'],
    ]);

    expect($response->respCode())->toBe('00')
        ->and($response->respCodeDesc())->toBe('Approved');
});

it('builds a failure message from the response params or action code', function (): void {
    expect((new SatimConfirmResponse(['params' => ['respCode_desc' => 'Declined']]))->message())->toBe('Declined')
        ->and((new SatimConfirmResponse(['actionCodeDescription' => 'Insufficient funds']))->message())->toBe('Insufficient funds');
});

it('exposes scalar fields', function (): void {
    $response = new SatimConfirmResponse([
        'actionCode' => 0,
        'approvalCode' => 'A1',
        'authorizationResponseId' => 'AR1',
        'OrderNumber' => 'ORD123',
        'SvfeResponse' => 'ok',
        'errorCode' => '0',
        'errorMessage' => 'none',
    ]);

    expect($response->actionCode())->toBe(0)
        ->and($response->approvalCode())->toBe('A1')
        ->and($response->authorizationResponseId())->toBe('AR1')
        ->and($response->orderNumber())->toBe('ORD123')
        ->and($response->svfeResponse())->toBe('ok')
        ->and($response->errorCode())->toBe(0)
        ->and($response->errorMessage())->toBe('none');
});
