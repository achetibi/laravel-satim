<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimResponseAccessor
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 22/06/2025
 *
 * @version 1.0.2
 */
readonly class SatimResponseAccessor
{
    public function __construct(
        private array $data
    ) {}

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public function getString(string $key, ?string $default = null): ?string
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (string) $value;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public function getInt(string $key, ?int $default = null): ?int
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (int) $value;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public function getFloat(string $key, ?float $default = null): ?float
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (float) $value;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public function getArray(string $key, array $default = []): array
    {
        $value = $this->data[$key] ?? $default;

        return empty($value) ? $default : (array) $value;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public function getEnum(string $key, string $enumClass, $default = null): mixed
    {
        $value = $this->getString($key);

        return ! is_null($value) && enum_exists($enumClass) && method_exists($enumClass, 'tryFrom') ? $enumClass::tryFrom($value) : $default;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 22/06/2025
     */
    public static function make(array $data): self
    {
        return new self($data);
    }
}
