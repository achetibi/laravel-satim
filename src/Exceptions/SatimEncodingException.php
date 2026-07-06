<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use JsonException;

final class SatimEncodingException extends SatimAbstractException
{
    public static function forJsonParams(JsonException $previous): self
    {
        return new self(
            message: __('satim::exceptions.json_encode_failed'),
            code: $previous->getCode(),
            previous: $previous,
        );
    }
}
