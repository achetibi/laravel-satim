<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Support\SatimResponseAccessor;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimRefundResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class SatimRefundResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fromResponse(array $response): SatimRefundResponse
    {
        $responseAccessor = SatimResponseAccessor::make($response);

        return new static(
            errorCode: $responseAccessor->getString('errorCode'),
            errorMessage: $responseAccessor->getString('errorMessage')
        );
    }
}
