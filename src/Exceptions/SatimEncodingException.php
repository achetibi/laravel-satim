<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use JsonException;
use LaravelSatim\Support\SatimTranslator;

final class SatimEncodingException extends SatimAbstractException
{
    public static function forJsonParams(JsonException $previous): self
    {
        return new self(
            message: SatimTranslator::get('satim::exceptions.json_encode_failed'),
            code: (int) $previous->getCode(),
            previous: $previous,
        );
    }
}
