<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\AbstractSatimResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim response classes', function () {
    expect(SatimRefundResponse::fromResponse(
        refundEndpoint([
            'errorCode' => '0',
            'ErrorMessage' => 'Success',
        ])
    ))
        ->toBeInstanceOf(SatimRefundResponse::class)
        ->toBeInstanceOf(AbstractSatimResponse::class)
        ->toBeInstanceOf(SatimResponseInterface::class);
});

it('can refund a with success response', function () {
    $response = SatimRefundResponse::fromResponse(
        refundEndpoint([
            'errorCode' => '0',
            'errorMessage' => 'Success',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBe('Success');
});

it('can not exceeds deposit amount', function () {
    $response = SatimRefundResponse::fromResponse(
        refundEndpoint([
            'errorCode' => '7',
            'errorMessage' => 'Refund amount exceeds deposited amount',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->errorCode)->toBe('7')
        ->and($response->errorMessage)->toBe('Refund amount exceeds deposited amount');
});

it('can confirm invalid orderId', function () {
    $response = SatimRefundResponse::fromResponse(
        refundEndpoint([
            'errorCode' => '6',
            'errorMessage' => 'No such order',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->errorCode)->toBe('6')
        ->and($response->errorMessage)->toBe('No such order');
});

it('can confirm invalid credentials', function () {
    $response = SatimRefundResponse::fromResponse(
        refundEndpoint([
            'errorCode' => '5',
            'errorMessage' => 'Access denied',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->errorCode)->toBe('5')
        ->and($response->errorMessage)->toBe('Access denied');
});
