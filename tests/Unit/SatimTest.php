<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\SatimHttpClient;
use LaravelSatim\Satim;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('resolves the configured currency whether it is given by name or by ISO value', function () {
    config()->set('satim.currency', 'DZD');
    expect((new Satim(new SatimHttpClient()))->getCurrency())->toBe(SatimCurrency::DZD);

    config()->set('satim.currency', '012');
    expect((new Satim(new SatimHttpClient()))->getCurrency())->toBe(SatimCurrency::DZD);

    config()->set('satim.currency', 'invalid');
    expect((new Satim(new SatimHttpClient()))->getCurrency())->toBe(SatimCurrency::fallback());
});

it('resolves the configured language whether it is given by name or by value', function () {
    config()->set('satim.language', 'FR');
    expect((new Satim(new SatimHttpClient()))->getLanguage())->toBe(SatimLanguage::FR);

    config()->set('satim.language', 'ar');
    expect((new Satim(new SatimHttpClient()))->getLanguage())->toBe(SatimLanguage::AR);

    config()->set('satim.language', 'invalid');
    expect((new Satim(new SatimHttpClient()))->getLanguage())->toBe(SatimLanguage::fallback());
});

it('registers an order against register.do', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/register.do');

        return Http::response(['errorCode' => '0', 'orderId' => 'ORD1', 'formUrl' => 'https://pay']);
    });

    app(SatimInterface::class)->register(SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ));
});

it('confirms an order against acknowledgeTransaction.do using mdOrder', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/public/acknowledgeTransaction.do');
        expect($request->data())->toHaveKey('mdOrder', 'ORDER123');
        expect($request->data())->not->toHaveKey('orderId');

        return Http::response(['ErrorCode' => '0', 'OrderStatus' => 2]);
    });

    app(SatimInterface::class)->confirm(SatimConfirmRequest::make(
        orderId: 'ORDER123'
    ));
});

it('refunds an order against refund.do', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('/refund.do');
        expect($request->data())->toHaveKey('orderId', 'ORDER123');
        expect($request->data())->toHaveKey('amount', 10050);

        return Http::response(['errorCode' => '0']);
    });

    app(SatimInterface::class)->refund(SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    ));
});
