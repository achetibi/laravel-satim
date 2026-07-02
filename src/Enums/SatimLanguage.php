<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Traits\EnumToArray;

enum SatimLanguage: string
{
    use EnumToArray;

    case AR = 'AR';
    case EN = 'EN';
    case FR = 'FR';

    public static function fallback(): SatimLanguage
    {
        return self::EN;
    }
}
