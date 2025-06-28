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
 * @name SatimRegisterResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class SatimRegisterResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public function __construct(
        public ?string $orderId = null,
        public ?string $formUrl = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null
    ) {
        parent::__construct(
            errorCode: $errorCode,
            errorMessage: $errorMessage
        );
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fromResponse(array $response): SatimRegisterResponse
    {
        $responseAccessor = SatimResponseAccessor::make($response);

        return new self(
            orderId: $responseAccessor->getString('orderId'),
            formUrl: $responseAccessor->getString('formUrl'),
            errorCode: $responseAccessor->getString('errorCode'),
            errorMessage: $responseAccessor->getString('errorMessage')
        );
    }
}
