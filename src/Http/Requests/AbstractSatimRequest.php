<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

abstract class AbstractSatimRequest
{
    protected function userName(): ?string
    {
        $value = config('satim.username');

        return is_string($value) ? $value : null;
    }

    protected function password(): ?string
    {
        $value = config('satim.password');

        return is_string($value) ? $value : null;
    }

    protected function terminal(): ?string
    {
        $value = config('satim.terminal');

        return is_string($value) ? $value : null;
    }
}
