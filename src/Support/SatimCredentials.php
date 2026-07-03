<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use LaravelSatim\Exceptions\SatimConfigurationException;

final readonly class SatimCredentials
{
    public function __construct(
        public string $userName,
        public string $password,
        public string $terminal,
    ) {
    }

    /**
     * @throws SatimConfigurationException
     */
    public static function fromConfig(mixed $userName, mixed $password, mixed $terminal): self
    {
        return new self(
            self::require($userName, 'username'),
            self::require($password, 'password'),
            self::require($terminal, 'terminal'),
        );
    }

    /**
     * @throws SatimConfigurationException
     */
    private static function require(mixed $value, string $key): string
    {
        if (! is_string($value) || $value === '') {
            throw new SatimConfigurationException("SATIM credential [{$key}] is not configured.");
        }

        return $value;
    }
}
