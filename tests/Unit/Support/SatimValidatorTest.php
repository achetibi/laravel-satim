<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Support\SatimValidator;

it('passes when every rule is satisfied', function () {
    SatimValidator::make()
        ->required('ORDER123', 'order number')
        ->alphanumeric('ORDER123', 'order number', 10)
        ->token('abc123', 'order id')
        ->url('https://example.com', 'return URL')
        ->amount(100.50)
        ->validate();

    expect(true)->toBeTrue();
});

it('reports the first error as the exception message and keeps them all', function () {
    try {
        SatimValidator::make()
            ->required('', 'order number')
            ->amount(10.0)
            ->validate();
        $this->fail('Expected a SatimValidationException.');
    } catch (SatimValidationException $e) {
        expect($e->getMessage())->toBe('The order number is required.')
            ->and($e->errors())->toBe(['The order number is required.', 'The amount must be at least 50.']);
    }
});

it('skips format rules for empty or null values', function () {
    $validator = SatimValidator::make()
        ->alphanumeric(null, 'udf2 field', 20)
        ->alphanumeric('', 'udf3 field', 20)
        ->token(null, 'order id')
        ->url(null, 'fail URL')
        ->maxLength(null, 'description', 512);

    expect($validator->errors())->toBe([]);
});

it('validates each rule type', function (callable $rule, ?string $message) {
    $validator = SatimValidator::make();
    $rule($validator);

    expect($validator->errors())->toBe($message === null ? [] : [$message]);
})->with([
    'alphanumeric ok' => [fn (SatimValidator $v) => $v->alphanumeric('ABC123', 'order number', 10), null],
    'alphanumeric too long' => [fn (SatimValidator $v) => $v->alphanumeric(str_repeat('a', 11), 'order number', 10), 'The order number must be alphanumeric and at most 10 characters.'],
    'alphanumeric special char' => [fn (SatimValidator $v) => $v->alphanumeric('A-1', 'order number', 10), 'The order number must be alphanumeric and at most 10 characters.'],
    'token with space' => [fn (SatimValidator $v) => $v->token('a b', 'order id'), 'The order id must be at most 20 characters and contain no spaces.'],
    'url invalid' => [fn (SatimValidator $v) => $v->url('not-a-url', 'return URL'), 'The return URL must be a valid URL of at most 512 characters.'],
    'max length' => [fn (SatimValidator $v) => $v->maxLength(str_repeat('a', 513), 'description', 512), 'The description must not be greater than 512 characters.'],
    'amount too small' => [fn (SatimValidator $v) => $v->amount(49.99), 'The amount must be at least 50.'],
    'amount too precise' => [fn (SatimValidator $v) => $v->amount(100.123), 'The amount must not have more than two decimal places.'],
    'amount too large' => [fn (SatimValidator $v) => $v->amount(10_000_000_000.0), 'The amount is too large.'],
]);
