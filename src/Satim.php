<?php

declare(strict_types=1);

namespace LaravelSatim;

use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Http\SatimErrorHandler;
use LaravelSatim\Http\SatimHttpClient;

readonly class Satim implements SatimInterface
{
    public function __construct(
        private SatimHttpClient $client,
        private SatimErrorHandler $errors,
    ) {
    }

    public function register(SatimRegisterRequest $request): SatimRegisterResponse
    {
        $response = $this->client->send('/register.do', $request);

        $this->errors->forRegister($response);

        return SatimRegisterResponse::fromResponse($response);
    }

    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse
    {
        $response = $this->client->send('/public/acknowledgeTransaction.do', $request);

        $this->errors->forConfirm($response);

        return SatimConfirmResponse::fromResponse($response);
    }

    public function refund(SatimRefundRequest $request): SatimRefundResponse
    {
        $response = $this->client->send('/refund.do', $request);

        $this->errors->forRefund($response);

        return SatimRefundResponse::fromResponse($response);
    }
}
