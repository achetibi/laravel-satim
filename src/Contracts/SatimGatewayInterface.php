<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Requests\SatimStatusRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Http\Responses\SatimStatusResponse;

interface SatimGatewayInterface
{
    public function register(SatimRegisterRequest $request): SatimRegisterResponse;

    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse;

    public function refund(SatimRefundRequest $request): SatimRefundResponse;

    public function status(SatimStatusRequest $request): SatimStatusResponse;
}
