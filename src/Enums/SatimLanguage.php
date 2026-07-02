<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum SatimLanguage: string
{
    case AR = 'AR';
    case EN = 'EN';
    case FR = 'FR';

    public static function fallback(): SatimLanguage
    {
        return self::EN;
    }
}
