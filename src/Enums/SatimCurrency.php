<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Traits\EnumToArray;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimCurrency
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
enum SatimCurrency: string
{
    use EnumToArray;

    case DZD = '012';
    // case EUR = '978';
    // case USD = '840';

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fallback(): SatimCurrency
    {
        return self::DZD;
    }
}
