<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Http\Requests\SatimRefundRequest;

it('implements the request contract', function () {
    expect(SatimRefundRequest::make(orderId: 'ORDER123', amount: 100.50))
        ->toBeInstanceOf(SatimRequestInterface::class);
});

it('exposes only business parameters (no credentials)', function () {
    $parameters = SatimRefundRequest::make(orderId: 'ORDER123', amount: 100.50)->parameters();

    expect($parameters)->toBe(['orderId' => 'ORDER123', 'amount' => 10050])
        ->and($parameters)->not->toHaveKey('userName');
});

it('converts float-sensitive amounts to cents without truncation', function () {
    expect(SatimRefundRequest::make(orderId: 'ORDER123', amount: 64.07)->parameters()['amount'])->toBe(6407);
});

it('validates order id and amount', function (array $args, string $message) {
    expect(fn () => SatimRefundRequest::make(...$args))
        ->toThrow(SatimValidationException::class, $message);
})->with([
    'empty order id' => [['orderId' => '', 'amount' => 100.0], 'The order id is required.'],
    'long order id' => [['orderId' => str_repeat('a', 21), 'amount' => 100.0], 'The order id must not be greater than 20 characters.'],
    'amount below minimum' => [['orderId' => 'O1', 'amount' => 49.99], 'The amount must be at least 50.'],
    'amount too precise' => [['orderId' => 'O1', 'amount' => 100.123], 'The amount must not have more than two decimal places.'],
]);
