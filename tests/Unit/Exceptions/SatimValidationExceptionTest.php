<?php

declare(strict_types=1);

use Illuminate\Support\MessageBag;
use LaravelSatim\Exceptions\SatimValidationException;

it('summarises with the first error message', function (): void {
    $exception = SatimValidationException::make([
        'orderNumber' => ['The order number is required.'],
        'amount' => ['The amount is invalid.'],
    ]);

    expect($exception->getMessage())->toBe('The order number is required.')
        ->and($exception->first())->toBe('The order number is required.')
        ->and($exception->first('amount'))->toBe('The amount is invalid.');
});

it('falls back to a generic summary when the bag is empty', function (): void {
    app()->setLocale('en');

    $exception = SatimValidationException::withErrors(new MessageBag());

    expect($exception->getMessage())->toBe('The request validation failed.');
});

it('exposes the underlying errors', function (): void {
    $exception = SatimValidationException::make(['amount' => ['Invalid amount.']]);

    expect($exception->has('amount'))->toBeTrue()
        ->and($exception->has('missing'))->toBeFalse()
        ->and($exception->messages())->toBe(['amount' => ['Invalid amount.']])
        ->and($exception->toArray())->toBe(['amount' => ['Invalid amount.']])
        ->and($exception->errors())->toBeInstanceOf(MessageBag::class);
});

it('returns null from first when the field has no error', function (): void {
    $exception = SatimValidationException::make(['amount' => ['Invalid amount.']]);

    expect($exception->first('orderNumber'))->toBeNull();
});
