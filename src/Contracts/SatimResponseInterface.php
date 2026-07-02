<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Http\Responses\AbstractSatimResponse;

interface SatimResponseInterface
{
    public static function fromResponse(array $response): AbstractSatimResponse;
}
