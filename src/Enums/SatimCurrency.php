<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Enums\Concerns\ResolvesFromNameOrValue;

enum SatimCurrency: string
{
    use ResolvesFromNameOrValue;

    case DZD = '012';
    // case EUR = '978';
    // case USD = '840';

    public static function fallback(): SatimCurrency
    {
        return self::DZD;
    }
}
