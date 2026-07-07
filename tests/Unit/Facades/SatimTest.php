<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Facades\Satim;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

it('resolves to the gateway contract', function (): void {
    expect(Satim::getFacadeRoot())->toBeInstanceOf(SatimGatewayInterface::class);
});

it('proxies calls to the underlying gateway', function (): void {
    $expected = new SatimRegisterResponse(['errorCode' => '0', 'formUrl' => 'https://pay.test']);

    $gateway = Mockery::mock(SatimGatewayInterface::class);
    $gateway->shouldReceive('register')->once()->andReturn($expected);
    Satim::swap($gateway);

    $response = Satim::register(new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'U1',
    ));

    expect($response)->toBe($expected);
});
