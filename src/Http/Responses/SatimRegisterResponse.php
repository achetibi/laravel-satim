<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Support\SatimResponseAccessor;

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
