<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\AbstractSatimResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim response classes', function () {
    expect(SatimRegisterResponse::fromResponse(
        confirmEndpoint([
            'errorCode' => '0',
            'orderId' => 'QNA1IhS444MlTAAAGN6I',
            'formUrl' => 'https://fake.satim.dz/payment/merchants/merchant1/payment_fr.html?mdOrder=QNA1IhS444MlTAAAGN6I',
        ])
    ))
        ->toBeInstanceOf(SatimRegisterResponse::class)
        ->toBeInstanceOf(AbstractSatimResponse::class)
        ->toBeInstanceOf(SatimResponseInterface::class);
});
