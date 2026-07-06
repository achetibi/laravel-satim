<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

final class SatimConnectionException extends SatimAbstractException
{
    public static function from(\Throwable $previous): self
    {
        return new self(
            message: __('satim::exceptions.connection_failed'),
            code: $previous->getCode(),
            previous: $previous
        );
    }
}
