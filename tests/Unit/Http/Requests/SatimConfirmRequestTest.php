<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Http\Requests\SatimConfirmRequest;

it('implements the request contract', function () {
    expect(SatimConfirmRequest::make(mdOrder: 'ORDER123'))
        ->toBeInstanceOf(SatimRequestInterface::class);
});

it('exposes the mdOrder parameter without credentials', function () {
    $parameters = SatimConfirmRequest::make(mdOrder: 'ORDER123', language: SatimLanguage::EN)->parameters();

    expect($parameters)->toBe(['mdOrder' => 'ORDER123', 'language' => 'EN'])
        ->and($parameters)->not->toHaveKey('userName')
        ->and($parameters)->not->toHaveKey('orderId');
});

it('leaves the language null when not provided', function () {
    expect(SatimConfirmRequest::make(mdOrder: 'ORDER123')->parameters()['language'])->toBeNull();
});

it('validates the mdOrder', function () {
    expect(fn () => SatimConfirmRequest::make(mdOrder: ''))
        ->toThrow(SatimValidationException::class, 'The order id is required.')
        ->and(fn () => SatimConfirmRequest::make(mdOrder: str_repeat('a', 21)))
        ->toThrow(SatimValidationException::class, 'The order id must be at most 20 characters and contain no spaces.')
        ->and(fn () => SatimConfirmRequest::make(mdOrder: 'has space'))
        ->toThrow(SatimValidationException::class, 'The order id must be at most 20 characters and contain no spaces.');
});

it('trims the mdOrder before validating', function () {
    expect(SatimConfirmRequest::make(mdOrder: '  ORDER123  ')->parameters()['mdOrder'])->toBe('ORDER123');
});

it('rejects an invalid language type', function () {
    expect(fn () => SatimConfirmRequest::make(mdOrder: 'ORDER123', language: 'EN'))
        ->toThrow(TypeError::class);
});
