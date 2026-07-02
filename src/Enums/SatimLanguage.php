<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Enums\Concerns\ResolvesFromNameOrValue;

enum SatimLanguage: string
{
    use ResolvesFromNameOrValue;

    case AR = 'AR';
    case EN = 'EN';
    case FR = 'FR';

    public static function fallback(): SatimLanguage
    {
        return self::EN;
    }
}
