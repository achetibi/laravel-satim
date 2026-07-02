<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Support\SatimResponseAccessor;

class SatimRefundResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public static function fromResponse(array $response): SatimRefundResponse
    {
        $responseAccessor = SatimResponseAccessor::make($response);

        return new static(
            errorCode: $responseAccessor->getString('errorCode'),
            errorMessage: $responseAccessor->getString('errorMessage')
        );
    }
}
