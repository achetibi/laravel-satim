<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\FundingType;
use LaravelSatim\Enums\Language;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

it('converts the amount to centimes and maps enums to gateway codes', function (): void {
    $request = new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 1500.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'ORD123',
        currency: Currency::DZD,
        language: Language::ARABIC,
        fundingType: FundingType::BILL_PAYMENT,
    );

    $payload = $request->payload();

    expect($payload['amount'])->toBe(150000)
        ->and($payload['currency'])->toBe('012')
        ->and($payload['language'])->toBe('ar')
        ->and($payload['returnUrl'])->toBe('https://shop.test/return')
        ->and($payload['jsonParams']['udf1'])->toBe('ORD123')
        ->and($payload['jsonParams']['fundingTypeIndicator'])->toBe('CP');
});

it('strips optional null fields from the payload', function (): void {
    $request = new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'ORD123',
    );

    $payload = $request->payload();

    expect($payload)->not->toHaveKey('failUrl')
        ->and($payload)->not->toHaveKey('description')
        ->and($payload['jsonParams'])->toBe(['udf1' => 'ORD123']);
});

it('exposes validation rules for every field', function (): void {
    $rules = (new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'ORD123',
    ))->rules();

    expect($rules)->toHaveKeys(['orderNumber', 'amount', 'returnUrl', 'udf1']);
});

it('maps a psr response to a register response', function (): void {
    $request = new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'ORD123',
    );

    $response = $request->toResponse(jsonResponse([
        'errorCode' => '0',
        'orderId' => 'abc',
        'formUrl' => 'https://pay.test/form',
    ]));

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->successful())->toBeTrue();
});
