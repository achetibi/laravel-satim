<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;

class SatimRefundResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    /**
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): SatimRefundResponse
    {
        $data = SatimResponseData::from($response);

        return new self(
            errorCode: $data->string('errorCode'),
            errorMessage: $data->string('errorMessage')
        );
    }
}
