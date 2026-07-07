<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimEncodingException;

it('wraps a json exception', function (): void {
    app()->setLocale('en');
    $previous = new JsonException('malformed utf-8');

    $exception = SatimEncodingException::forJsonParams($previous);

    expect($exception->getPrevious())->toBe($previous)
        ->and($exception->getMessage())->toBe('Unable to encode the request payload as JSON.');
});
