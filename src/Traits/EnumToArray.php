<?php

declare(strict_types=1);

namespace LaravelSatim\Traits;

trait EnumToArray
{
    public static function values(): array
    {
        if (method_exists(__CLASS__, 'cases')) {
            return array_column(static::cases(), 'value');
        }

        return [];
    }

    public static function fromName(string $name): ?static
    {
        $name = strtoupper($name);
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
