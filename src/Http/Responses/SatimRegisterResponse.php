<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;

class SatimRegisterResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public function __construct(
        public readonly ?string $orderId = null,
        public readonly ?string $formUrl = null,
        ?string $errorCode = null,
        ?string $errorMessage = null
    ) {
        parent::__construct(
            errorCode: $errorCode,
            errorMessage: $errorMessage
        );
    }

    public static function fromResponse(array $response): SatimRegisterResponse
    {
        $data = SatimResponseData::from($response);

        return new self(
            orderId: $data->string('orderId'),
            formUrl: $data->string('formUrl'),
            errorCode: $data->string('errorCode'),
            errorMessage: $data->string('errorMessage')
        );
    }
}
