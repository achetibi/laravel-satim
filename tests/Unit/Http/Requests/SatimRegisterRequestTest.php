<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;
use LaravelSatim\Http\Requests\AbstractSatimRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends end implements satim request classes', function () {
    expect(SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))
        ->toBeInstanceOf(SatimRegisterRequest::class)
        ->toBeInstanceOf(AbstractSatimRequest::class)
        ->toBeInstanceOf(SatimRequestInterface::class);
});

it('can create a valid register request', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request)
        ->toBeInstanceOf(SatimRegisterRequest::class)
        ->and($request->orderNumber)->toBe('ORDER123')
        ->and($request->amount)->toBe(100.50)
        ->and($request->returnUrl)->toBe('https://example.com/return')
        ->and($request->udf1)->toBe('udf1');
});

it('can create a register request with all optional parameters', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 250.75,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2',
        udf3: 'udf3',
        udf4: 'udf4',
        udf5: 'udf5',
        failUrl: 'https://example.com/fail',
        description: 'Test payment',
        currency: SatimCurrency::DZD,
        language: SatimLanguage::EN
    );

    expect($request->orderNumber)->toBe('ORDER123')
        ->and($request->amount)->toBe(250.75)
        ->and($request->returnUrl)->toBe('https://example.com/return')
        ->and($request->udf1)->toBe('udf1')
        ->and($request->udf2)->toBe('udf2')
        ->and($request->udf3)->toBe('udf3')
        ->and($request->udf4)->toBe('udf4')
        ->and($request->udf5)->toBe('udf5')
        ->and($request->failUrl)->toBe('https://example.com/fail')
        ->and($request->description)->toBe('Test payment')
        ->and($request->currency)->toBe(SatimCurrency::DZD)
        ->and($request->language)->toBe(SatimLanguage::EN);
});

it('can converts to array format correctly', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 250.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        description: 'Test payment'
    );

    $array = $request->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderNumber', 'amount', 'currency',
            'returnUrl', 'failUrl', 'description', 'language', 'jsonParams',
        ])
        ->and($array['orderNumber'])->toBe('ORDER123')
        ->and($array['amount'])->toBe(250.50)
        ->and($array['returnUrl'])->toBe('https://example.com/return')
        ->and($array['description'])->toBe('Test payment')
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password')
        ->and($array['jsonParams'])
        ->toBeArray()
        ->toHaveKeys([
            'force_terminal_id', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5',
        ])
        ->and($array['jsonParams']['force_terminal_id'])->toBe('test_terminal')
        ->and($array['jsonParams']['udf1'])->toBe('udf1');
});

it('can converts to request format correctly', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        currency: SatimCurrency::DZD,
        language: SatimLanguage::FR
    );

    $requestData = $request->toRequest();

    expect($requestData)
        ->toHaveKeys([
            'userName', 'password', 'orderNumber', 'amount', 'currency',
            'returnUrl', 'failUrl', 'description', 'language', 'jsonParams',
        ])
        ->and($requestData['orderNumber'])->toBe('ORDER123')
        ->and($requestData['amount'])->toBe(10050)
        ->and($requestData['currency'])->toBe('012')
        ->and($requestData['language'])->toBe('FR')
        ->and($requestData['userName'])->toBe('test_username')
        ->and($requestData['password'])->toBe('test_password')
        ->and($requestData['jsonParams'])->toBeString();
});

it('can create a register request with a minimum amount', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 50.00,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request->amount)->toBe(50.00);
});

it('can create a register request with amounts proper precision', function () {
    $amounts = [50.00, 100.50, 999.99, 1234.56];

    foreach ($amounts as $amount) {
        $request = SatimRegisterRequest::make(
            orderNumber: 'ORDER123',
            amount: $amount,
            returnUrl: 'https://example.com/return',
            udf1: 'udf1'
        );

        expect($request->amount)->toBe($amount);
    }
});

it('can converts amount to cents correctly in request format', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 123.45,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    $requestData = $request->toRequest();
    expect($requestData['amount'])->toBe(12345);
});

it('can encodes jsonParams correctly in request format', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.00,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2'
    );

    $requestData = $request->toRequest();
    $jsonParams = json_decode($requestData['jsonParams'], true);

    expect($jsonParams)
        ->toHaveKeys([
            'force_terminal_id', 'udf1', 'udf2',
        ])
        ->and($jsonParams['force_terminal_id'])->toBe('test_terminal')
        ->and($jsonParams['udf1'])->toBe('udf1')
        ->and($jsonParams['udf2'])->toBe('udf2');
});

it('can create a register request with orderNumber max length', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: str_repeat('a', 10),
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request->orderNumber)->toHaveLength(10);
});

it('can create a register request with a valid currency', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        currency: SatimCurrency::DZD
    );

    expect($request->currency)->toBe(SatimCurrency::DZD);
});

it('can create a register request with a valid language', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        language: SatimLanguage::FR
    );

    expect($request->language)->toBe(SatimLanguage::FR);
});

it('can create a register request with max udf length', function () {
    $validUdf = str_repeat('a', 20);

    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: $validUdf,
        udf2: $validUdf,
        udf3: $validUdf,
        udf4: $validUdf,
        udf5: $validUdf
    );

    expect($request->udf1)->toBe($validUdf)
        ->and($request->udf2)->toBe($validUdf)
        ->and($request->udf3)->toBe($validUdf)
        ->and($request->udf4)->toBe($validUdf)
        ->and($request->udf5)->toBe($validUdf);
});

it('can filters jsonParams null values', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2'
    );

    $requestData = $request->toRequest();
    $jsonParams = json_decode($requestData['jsonParams'], true);

    expect($jsonParams)
        ->toBeArray()
        ->toHaveKeys(['force_terminal_id', 'udf1', 'udf2'])
        ->and($jsonParams)->not()->toHaveKey('udf3')
        ->and($jsonParams)->not()->toHaveKey('udf4')
        ->and($jsonParams)->not()->toHaveKey('udf5');
});

it('throws when orderNumber is empty string', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: '',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The order number field is required.');

        return $exception;
    });
});

it('throws when orderNumber exceeds 10 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: str_repeat('a', 11),
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The order number field must not be greater than 10 characters.');

        return $exception;
    });
});

it('throws when amount is less then 50', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 49.99,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The amount field must be at least 50.');

        return $exception;
    });
});

it('throws when amount exceeds two decimal places', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.123,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The amount field must have 0-2 decimal places.');

        return $exception;
    });
});

it('throws when returnUrl is empty string', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: '',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The return url field is required.');

        return $exception;
    });
});

it('throws when returnUrl is invalid', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'not-a-valid-url',
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The return url field must be a valid URL.');

        return $exception;
    });
});

it('throws when returnUrl exceeds 512 characters', function () {
    $longUrl = 'https://example.com/'.str_repeat('a', 500);

    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: $longUrl,
        udf1: 'udf1'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The return url field must not be greater than 512 characters.');

        return $exception;
    });
});

it('throws when failUrl is invalid', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        failUrl: 'not-a-valid-url'
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The fail url field must be a valid URL.');

        return $exception;
    });
});

it('throws when failUrl exceeds 512 characters', function () {
    $longUrl = 'https://example.com/'.str_repeat('a', 500);

    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        failUrl: $longUrl
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The fail url field must not be greater than 512 characters.');

        return $exception;
    });
});

it('throws when description exceeds 512 characters', function () {
    $longDescription = str_repeat('a', 513);

    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        description: $longDescription
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The description field must not be greater than 512 characters.');

        return $exception;
    });
});

it('throws when udf1 is empty string', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: ''
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf1 field is required.');

        return $exception;
    });
});

it('throws when udf1 exceeds 20 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf1 field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when udf2 exceeds 20 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf2 field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when udf3 exceeds 20 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2',
        udf3: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf3 field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when udf4 exceeds 20 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2',
        udf3: 'udf3',
        udf4: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf4 field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when udf5 exceeds 20 characters', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2',
        udf3: 'udf3',
        udf4: 'udf4',
        udf5: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The json params.udf5 field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when language is invalid type', function () {
    expect(fn () => SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        language: 'EN'
    ))->toThrow(TypeError::class);
});
