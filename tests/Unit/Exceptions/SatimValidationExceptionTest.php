<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimException;
use LaravelSatim\Exceptions\SatimValidationException;

it('exposes the collected validation errors', function () {
    $exception = new SatimValidationException('The order id is required.', [
        'The order id is required.',
        'The amount must be at least 50.',
    ]);

    expect($exception->getMessage())->toBe('The order id is required.')
        ->and($exception->errors())->toBe([
            'The order id is required.',
            'The amount must be at least 50.',
        ]);
});

it('defaults to an empty error list', function () {
    expect((new SatimValidationException('x'))->errors())->toBe([]);
});

it('extends the base SatimException', function () {
    expect(new SatimValidationException('x'))->toBeInstanceOf(SatimException::class);
});
