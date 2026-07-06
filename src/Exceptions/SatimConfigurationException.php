<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

final class SatimConfigurationException extends SatimAbstractException
{
    public static function missing(string $key): self
    {
        return new self(__('satim::exceptions.config.missing', ['key' => $key]));
    }

    public static function invalidEnvironment(string $value): self
    {
        return new self(__('satim::exceptions.config.invalid_environment', ['value' => $value]));
    }

    public static function missingBaseUrl(string $env): self
    {
        return new self(__('satim::exceptions.config.missing_base_url', ['env' => $env]));
    }

    public static function invalidValue(string $key, string $value): self
    {
        return new self(__('satim::exceptions.config.invalid_value', ['key' => $key, 'value' => $value]));
    }
}
