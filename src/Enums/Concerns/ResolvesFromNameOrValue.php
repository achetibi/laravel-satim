<?php

declare(strict_types=1);

namespace LaravelSatim\Enums\Concerns;

/**
 * @phpstan-require-implements \BackedEnum
 */
trait ResolvesFromNameOrValue
{
    public static function resolve(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        foreach (self::cases() as $case) {
            if (strcasecmp($case->value, $value) === 0 || strcasecmp($case->name, $value) === 0) {
                return $case;
            }
        }

        return null;
    }
}
