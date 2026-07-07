<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use LaravelSatim\Support\SatimTranslator;
use Throwable;

final class SatimConnectionException extends SatimAbstractException
{
    public static function from(Throwable $previous): self
    {
        return new self(
            message: SatimTranslator::get('satim::exceptions.connection_failed'),
            code: (int) $previous->getCode(),
            previous: $previous,
        );
    }
}
