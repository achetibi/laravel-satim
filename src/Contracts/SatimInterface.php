<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimApiServerException;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

interface SatimInterface
{
    public function register(SatimRegisterRequest $request): SatimRegisterResponse;

    /**
     * @throws SatimApiServerException
     */
    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse;

    /**
     * @throws SatimApiServerException
     */
    public function refund(SatimRefundRequest $request): SatimRefundResponse;
}
