<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Traits\EnumToArray;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimLanguage
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
enum SatimLanguage: string
{
    use EnumToArray;

    case AR = 'AR';
    case EN = 'EN';
    case FR = 'FR';

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fallback(): SatimLanguage
    {
        return self::EN;
    }
}
