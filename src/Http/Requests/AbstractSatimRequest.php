<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

abstract class AbstractSatimRequest
{
    protected function userName(): ?string
    {
        return config('satim.username');
    }

    protected function password(): ?string
    {
        return config('satim.password');
    }

    protected function terminal(): ?string
    {
        return config('satim.terminal');
    }
}
