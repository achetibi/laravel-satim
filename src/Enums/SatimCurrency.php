<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum SatimCurrency: string
{
    case DZD = '012';
    // case EUR = '978';
    // case USD = '840';

    public static function fallback(): SatimCurrency
    {
        return self::DZD;
    }
}
