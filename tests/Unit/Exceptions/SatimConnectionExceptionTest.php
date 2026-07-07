<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimConnectionException;

it('wraps the previous throwable and preserves its code', function (): void {
    app()->setLocale('en');
    $previous = new RuntimeException('boom', 42);

    $exception = SatimConnectionException::from($previous);

    expect($exception->getPrevious())->toBe($previous)
        ->and($exception->getCode())->toBe(42)
        ->and($exception->getMessage())->toBe('Unable to reach the payment gateway.');
});
