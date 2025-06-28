<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;
use LaravelSatim\Http\Requests\AbstractSatimRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim request classes', function () {
    expect(SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    ))
        ->toBeInstanceOf(SatimRefundRequest::class)
        ->toBeInstanceOf(AbstractSatimRequest::class)
        ->toBeInstanceOf(SatimRequestInterface::class);
});

it('can create a valid refund request', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    );

    expect($request)
        ->toBeInstanceOf(SatimRefundRequest::class)
        ->and($request->orderId)->toBe('ORDER123')
        ->and($request->amount)->toBe(100.50);
});

it('can converts to array format correctly', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 250.50
    );

    $array = $request->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderId', 'amount',
        ])
        ->and($array['orderId'])->toBe('ORDER123')
        ->and($array['amount'])->toBe(250.50)
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password');
});

it('can converts to request format correctly', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    );

    $requestData = $request->toRequest();

    expect($requestData)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderId', 'amount',
        ])
        ->and($requestData['amount'])->toBe(10050) // The amount should be in cents
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['userName'])->toBe('test_username')
        ->and($requestData['password'])->toBe('test_password');
});

it('can converts amount to cents correctly in request format', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 999.99
    );

    $requestData = $request->toRequest();

    expect($requestData['amount'])->toBe(99999);
});

it('can create a refund request with orderId max length', function () {
    $request = SatimRefundRequest::make(
        orderId: str_repeat('a', 20),
        amount: 100.0
    );

    expect($request->orderId)->toHaveLength(20);
});

it('can create a refund request with a minimum amount', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 50.00
    );

    expect($request->amount)->toBe(50.00);
});

it('throws when orderId is empty string', function () {
    expect(fn () => SatimRefundRequest::make(
        orderId: '',
        amount: 100.0
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The order id field is required.');

        return $exception;
    });
});

it('throws when orderId exceeds 20 characters', function () {
    expect(fn () => SatimRefundRequest::make(
        orderId: str_repeat('a', 21),
        amount: 100.0
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The order id field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when amount is less then 50', function () {
    expect(fn () => SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 49.99
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The amount field must be at least 50.');

        return $exception;
    });
});

it('throws when amount exceeds two decimal places', function () {
    expect(fn () => SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.123
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The amount field must have 0-2 decimal places.');

        return $exception;
    });
});
