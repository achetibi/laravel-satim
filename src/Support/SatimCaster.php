<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use DateMalformedStringException;
use DateTimeImmutable;

final class SatimCaster
{
    public static function string(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    public static function integer(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    public static function float(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    public static function boolean(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value) || is_int($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        }

        return null;
    }

    public static function dateTime(mixed $value): ?DateTimeImmutable
    {
        if (! is_numeric($value)) {
            return null;
        }

        try {
            $milliseconds = (int) $value;
            $seconds = intdiv($milliseconds, 1000);
            $fraction = str_pad((string) abs($milliseconds % 1000), 3, '0', STR_PAD_LEFT);

            return new DateTimeImmutable('@' . $seconds . '.' . $fraction);
        } catch (DateMalformedStringException) {
            return null;
        }
    }
}
