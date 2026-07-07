<?php

declare(strict_types=1);

use LaravelSatim\Http\Responses\SatimRegisterResponse;

it('casts a string error code to an integer', function (): void {
    expect((new SatimRegisterResponse(['errorCode' => '0']))->errorCode())->toBe(0)
        ->and((new SatimRegisterResponse(['errorCode' => '5']))->errorCode())->toBe(5);
});

it('reads the error code from either casing', function (): void {
    expect((new SatimRegisterResponse(['ErrorCode' => '5']))->errorCode())->toBe(5);
});

it('is successful only with error code 0 and a form url', function (): void {
    expect((new SatimRegisterResponse(['errorCode' => '0', 'formUrl' => 'https://pay.test']))->successful())->toBeTrue()
        ->and((new SatimRegisterResponse(['errorCode' => '0']))->successful())->toBeFalse()
        ->and((new SatimRegisterResponse(['errorCode' => '5', 'formUrl' => 'https://pay.test']))->successful())->toBeFalse();
});

it('exposes the order id, form url and error message', function (): void {
    $response = new SatimRegisterResponse([
        'errorCode' => '0',
        'orderId' => 'abc123',
        'formUrl' => 'https://pay.test/form',
        'errorMessage' => 'ok',
    ]);

    expect($response->orderId())->toBe('abc123')
        ->and($response->formUrl())->toBe('https://pay.test/form')
        ->and($response->errorMessage())->toBe('ok');
});
