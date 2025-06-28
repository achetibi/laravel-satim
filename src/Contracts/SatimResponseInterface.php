<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Http\Responses\AbstractSatimResponse;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimResponseInterface
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
interface SatimResponseInterface
{
    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fromResponse(array $response): AbstractSatimResponse;
}
