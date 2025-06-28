<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;
use LaravelSatim\Http\Requests\AbstractSatimRequest;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim request classes', function () {
    expect(SatimConfirmRequest::make(
        orderId: 'ORDER123'
    ))
        ->toBeInstanceOf(SatimConfirmRequest::class)
        ->toBeInstanceOf(AbstractSatimRequest::class)
        ->toBeInstanceOf(SatimRequestInterface::class);
});

it('can create a confirm request without language', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123'
    );

    expect($request)
        ->toBeInstanceOf(SatimConfirmRequest::class)
        ->and($request->orderId)->toBe('ORDER123')
        ->and($request->language)->toBeNull();
});

it('can create a confirm request with a valid language', function () {
    foreach (SatimLanguage::cases() as $language) {
        $request = SatimConfirmRequest::make(
            orderId: 'ORDER123',
            language: $language
        );

        expect($request)
            ->toBeInstanceOf(SatimConfirmRequest::class)
            ->and($request->language)->toBeInstanceOf(SatimLanguage::class)
            ->and($request->language)->toBe($language);
    }
});

it('can create a confirm request with orderId max length', function () {
    $request = SatimConfirmRequest::make(
        orderId: str_repeat('a', 20)
    );

    expect($request->orderId)->toHaveLength(20);
});

it('can converts to array format correctly', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: SatimLanguage::EN
    );

    $array = $request->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderId', 'language',
        ])
        ->and($array['orderId'])->toBe('ORDER123')
        ->and($array['language'])->toBe(SatimLanguage::EN)
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password');
});

it('can converts to request format correctly', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: SatimLanguage::EN
    );

    $requestData = $request->toRequest();

    expect($requestData)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderId', 'language',
        ])
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['language'])->toBe(SatimLanguage::EN->value);
});

it('can converts to request with null language', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123'
    );

    $requestData = $request->toRequest();

    expect($requestData)
        ->toBeArray()
        ->toHaveKeys([
            'userName', 'password', 'orderId', 'language',
        ])
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['language'])->toBeNull();
});

it('throws when orderId exceeds 20 characters', function () {
    expect(fn () => SatimConfirmRequest::make(
        orderId: str_repeat('a', 21)
    ))->toThrow(function (SatimInvalidArgumentException $exception) {
        expect($exception)
            ->toBeInstanceOf(SatimInvalidArgumentException::class)
            ->and($exception->getMessage())->toBe('The order id field must not be greater than 20 characters.');

        return $exception;
    });
});

it('throws when orderId is empty string', function () {
    expect(fn () => SatimConfirmRequest::make(
        orderId: ''
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('throws when orderId is null', function () {
    expect(fn () => SatimConfirmRequest::make(
        orderId: null
    ))->toThrow(TypeError::class);
});

it('throws when orderId is invalid type', function () {
    expect(fn () => SatimConfirmRequest::make(
        orderId: 123456
    ))->toThrow(TypeError::class);
});

it('throws when language is invalid type', function () {
    expect(fn () => SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: 'EN'
    ))->toThrow(TypeError::class);
});
