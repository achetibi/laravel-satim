<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

interface SatimInterface
{
    /**
     * @throws SatimConnectionException
     * @throws SatimResponseException
     */
    public function register(SatimRegisterRequest $request): SatimRegisterResponse;

    /**
     * @throws SatimConnectionException
     * @throws SatimResponseException
     */
    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse;

    /**
     * @throws SatimConnectionException
     * @throws SatimResponseException
     */
    public function refund(SatimRefundRequest $request): SatimRefundResponse;
}
