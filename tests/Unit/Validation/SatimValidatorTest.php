<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Validation\SatimValidator;

function makeValidator(): SatimValidator
{
    return new SatimValidator(app(ValidatorFactory::class));
}

function invalidRegisterRequest(): SatimRegisterRequest
{
    return new SatimRegisterRequest(
        orderNumber: '',
        amount: 50.00,
        returnUrl: 'not-a-url',
        udf1: 'U1',
    );
}

function validRegisterRequest(): SatimRegisterRequest
{
    return new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'U1',
    );
}

it('passes a valid request', function (): void {
    makeValidator()->validate(validRegisterRequest());
})->throwsNoExceptions();

it('throws a validation exception carrying the field errors', function (): void {
    app()->setLocale('en');

    try {
        makeValidator()->validate(invalidRegisterRequest());
        $this->fail('Expected a SatimValidationException.');
    } catch (SatimValidationException $e) {
        expect($e->has('orderNumber'))->toBeTrue()
            ->and($e->has('returnUrl'))->toBeTrue();
    }
});

it('uses the package translated messages', function (): void {
    app()->setLocale('en');

    try {
        makeValidator()->validate(invalidRegisterRequest());
    } catch (SatimValidationException $e) {
        expect($e->first('returnUrl'))->toBe('The return url field must be a valid URL.');
    }
});

it('does not validate excluded fields', function (): void {
    makeValidator()->exclude('orderNumber', 'returnUrl')->validate(invalidRegisterRequest());
})->throwsNoExceptions();

it('applies merged rules', function (): void {
    makeValidator()->merge(['udf1' => ['max:1']])->validate(validRegisterRequest());
})->throws(SatimValidationException::class);
