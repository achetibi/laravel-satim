<?php

declare(strict_types=1);

namespace LaravelSatim\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Requests\SatimStatusRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Http\Responses\SatimStatusResponse;

/**
 * @method static SatimRegisterResponse register(SatimRegisterRequest $request)
 * @method static SatimConfirmResponse confirm(SatimConfirmRequest $request)
 * @method static SatimRefundResponse refund(SatimRefundRequest $request)
 * @method static SatimStatusResponse status(SatimStatusRequest $request)
 *
 * @see \LaravelSatim\SatimGateway
 */
class Satim extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SatimGatewayInterface::class;
    }
}
