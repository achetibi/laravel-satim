<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Exceptions\SatimAuthenticationException;
use LaravelSatim\Exceptions\SatimPaymentException;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

function satim(): SatimInterface
{
    return app(SatimInterface::class);
}

it('registers an order against register.do and returns a response', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/register.do');

        return Http::response(['errorCode' => '0', 'orderId' => 'ORD1', 'formUrl' => 'https://pay']);
    });

    $response = satim()->register(SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://merchant.test/return',
        udf1: 'udf1',
    ));

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBe('ORD1')
        ->and($response->formUrl)->toBe('https://pay');
});

it('confirms an order against acknowledgeTransaction.do using mdOrder', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/public/acknowledgeTransaction.do');
        expect($request->data())->toHaveKey('mdOrder', 'ORDER123');

        return Http::response(['ErrorCode' => '0', 'OrderStatus' => 2, 'params' => ['respCode' => '00']]);
    });

    expect(satim()->confirm(SatimConfirmRequest::make(mdOrder: 'ORDER123')))
        ->toBeInstanceOf(SatimConfirmResponse::class);
});

it('refunds an order against refund.do', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/refund.do');
        expect($request->data())->toHaveKey('orderId', 'ORDER123')
            ->and($request->data())->toHaveKey('amount', 10050);

        return Http::response(['errorCode' => '0', 'errorMessage' => 'Success']);
    });

    expect(satim()->refund(SatimRefundRequest::make(orderId: 'ORDER123', amount: 100.50))->errorMessage)
        ->toBe('Success');
});

it('throws a payment exception when register returns an error code', function () {
    Http::fake(['*' => Http::response(['errorCode' => 1, 'errorMessage' => 'Order already processed.'])]);

    satim()->register(SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://merchant.test/return',
        udf1: 'udf1',
    ));
})->throws(SatimPaymentException::class, 'Order already processed.');

it('throws an authentication exception when confirm returns code 5', function () {
    Http::fake(['*' => Http::response(['ErrorCode' => 5, 'ErrorMessage' => 'Access is denied.'])]);

    satim()->confirm(SatimConfirmRequest::make(mdOrder: 'ORDER123'));
})->throws(SatimAuthenticationException::class, 'Access is denied.');

it('returns a declined confirm response without throwing (ErrorCode 3)', function () {
    Http::fake(['*' => Http::response([
        'ErrorCode' => '3',
        'OrderStatus' => 6,
        'actionCode' => 116,
        'params' => ['respCode' => '51', 'respCode_desc' => 'Crédit insuffisant'],
    ])]);

    $response = satim()->confirm(SatimConfirmRequest::make(mdOrder: 'ORDER123'));

    expect($response->cardBalanceInsufficient())->toBeTrue()
        ->and($response->fail())->toBeTrue();
});
