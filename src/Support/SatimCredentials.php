<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use LaravelSatim\Exceptions\SatimConfigurationException;

final readonly class SatimCredentials
{
    private const array MAX_LENGTHS = [
        'username' => 30,
        'password' => 30,
        'terminal' => 16
    ];

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
            self::sanitize($userName, 'username'),
            self::sanitize($password, 'password'),
            self::sanitize($terminal, 'terminal'),
        );
    }

    /**
     * Credentials are opaque, bank-issued secrets: we trim and bound their
     * length (per the SATIM table) but do not enforce a character set, to avoid
     * locking out a merchant over a legitimate special character.
     *
     * @throws SatimConfigurationException
     */
    private static function sanitize(mixed $value, string $key): string
    {
        if (! is_string($value) || trim($value) === '') {
            throw new SatimConfigurationException("SATIM credential [{$key}] is not configured.");
        }

        $value = trim($value);
        $max = self::MAX_LENGTHS[$key];

        if (mb_strlen($value) > $max) {
            throw new SatimConfigurationException("SATIM credential [{$key}] must not be greater than {$max} characters.");
        }

        return $value;
    }
}
