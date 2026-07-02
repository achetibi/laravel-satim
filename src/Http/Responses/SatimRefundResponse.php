<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;

class SatimRefundResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public static function fromResponse(array $response): SatimRefundResponse
    {
        $data = SatimResponseData::from($response);

        return new static(
            errorCode: $data->string('errorCode'),
            errorMessage: $data->string('errorMessage')
        );
    }
}
