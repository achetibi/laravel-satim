<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimFundingType;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

it('implements the request contract', function () {
    expect(SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
    ))->toBeInstanceOf(SatimRequestInterface::class);
});

it('exposes only business parameters (no credentials)', function () {
    $parameters = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        currency: SatimCurrency::DZD,
        language: SatimLanguage::FR,
    )->parameters();

    expect($parameters)
        ->toHaveKeys(['orderNumber', 'amount', 'currency', 'returnUrl', 'failUrl', 'description', 'language', 'jsonParams'])
        ->and($parameters)->not->toHaveKey('userName')
        ->and($parameters)->not->toHaveKey('password')
        ->and($parameters['amount'])->toBe(10050)
        ->and($parameters['currency'])->toBe('012')
        ->and($parameters['language'])->toBe('FR')
        ->and($parameters['jsonParams'])->toBe(['udf1' => 'udf1']);
});

it('includes the funding type indicator in jsonParams when provided', function (SatimFundingType $type, string $expected) {
    $parameters = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.00,
        returnUrl: 'https://merchant.test/return',
        udf1: 'udf1',
        fundingType: $type,
    )->parameters();

    expect($parameters['jsonParams'])->toBe(['udf1' => 'udf1', 'fundingTypeIndicator' => $expected]);
})->with([
    'CP' => [SatimFundingType::BILL_PAYMENT, 'CP'],
    '698' => [SatimFundingType::BILL_PAYMENT_698, '698'],
]);

it('keeps jsonParams as an array without force_terminal_id and filters empty udfs', function () {
    $parameters = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.00,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf3: 'udf3',
    )->parameters();

    expect($parameters['jsonParams'])->toBe(['udf1' => 'udf1', 'udf3' => 'udf3'])
        ->and($parameters['jsonParams'])->not->toHaveKey('force_terminal_id');
});

it('converts float-sensitive amounts to cents without truncation', function () {
    $parameters = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 64.07,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
    )->parameters();

    expect($parameters['amount'])->toBe(6407);
});

it('validates business fields natively', function (array $args, string $message) {
    expect(fn () => SatimRegisterRequest::make(...$args))
        ->toThrow(SatimValidationException::class, $message);
})->with([
    'empty order number' => [['orderNumber' => '', 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => 'u'], 'The order number is required.'],
    'long order number' => [['orderNumber' => str_repeat('a', 11), 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => 'u'], 'The order number must be alphanumeric and at most 10 characters.'],
    'non-alphanumeric order number' => [['orderNumber' => 'ORD-123', 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => 'u'], 'The order number must be alphanumeric and at most 10 characters.'],
    'amount below minimum' => [['orderNumber' => 'O1', 'amount' => 49.99, 'returnUrl' => 'https://x.test', 'udf1' => 'u'], 'The amount must be at least 50.'],
    'amount too precise' => [['orderNumber' => 'O1', 'amount' => 100.123, 'returnUrl' => 'https://x.test', 'udf1' => 'u'], 'The amount must not have more than two decimal places.'],
    'empty return url' => [['orderNumber' => 'O1', 'amount' => 100.0, 'returnUrl' => '', 'udf1' => 'u'], 'The return URL is required.'],
    'invalid return url' => [['orderNumber' => 'O1', 'amount' => 100.0, 'returnUrl' => 'not-a-url', 'udf1' => 'u'], 'The return URL must be a valid URL of at most 512 characters.'],
    'empty udf1' => [['orderNumber' => 'O1', 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => ''], 'The udf1 field is required.'],
    'non-alphanumeric udf2' => [['orderNumber' => 'O1', 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => 'u', 'udf2' => 'a b'], 'The udf2 field must be alphanumeric and at most 20 characters.'],
    'long udf2' => [['orderNumber' => 'O1', 'amount' => 100.0, 'returnUrl' => 'https://x.test', 'udf1' => 'u', 'udf2' => str_repeat('a', 21)], 'The udf2 field must be alphanumeric and at most 20 characters.'],
]);

it('trims string inputs before validating and sending', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: '  ORDER123  ',
        amount: 100.50,
        returnUrl: '  https://merchant.test/return  ',
        udf1: '  udf1  ',
    );

    expect($request->orderNumber)->toBe('ORDER123')
        ->and($request->returnUrl)->toBe('https://merchant.test/return')
        ->and($request->parameters()['jsonParams'])->toBe(['udf1' => 'udf1']);
});

it('collects every validation error on the exception', function () {
    try {
        SatimRegisterRequest::make(orderNumber: '', amount: 10.0, returnUrl: '', udf1: '');
        $this->fail('Expected a SatimValidationException.');
    } catch (SatimValidationException $e) {
        expect($e->errors())->toContain('The order number is required.')
            ->and($e->errors())->toContain('The amount must be at least 50.')
            ->and($e->errors())->toContain('The return URL is required.')
            ->and($e->errors())->toContain('The udf1 field is required.');
    }
});
