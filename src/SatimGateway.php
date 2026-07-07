<?php

declare(strict_types=1);

namespace LaravelSatim;

use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

final readonly class SatimGateway implements SatimGatewayInterface
{
    public function __construct(
        private SatimHttpClientInterface $client,
        private SatimValidatorInterface $validator,
    ) {
    }

    public function register(SatimRegisterRequest $request): SatimRegisterResponse
    {
        /**
         * @var SatimRegisterResponse
         */
        return $this->send('/register.do', $request);
    }

    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse
    {
        /**
         * @var SatimConfirmResponse
         */
        return $this->send('/public/acknowledgeTransaction.do', $request);
    }

    public function refund(SatimRefundRequest $request): SatimRefundResponse
    {
        /**
         * @var SatimRefundResponse
         */
        return $this->send('/refund.do', $request);
    }

    private function send(string $endpoint, SatimRequestInterface $request): SatimResponseInterface
    {
        $this->validator->validate($request);

        return $request->toResponse(
            $this->client->send($endpoint, $request)
        );
    }
}
