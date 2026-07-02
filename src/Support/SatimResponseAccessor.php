<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

readonly class SatimResponseAccessor
{
    public function __construct(
        private array $data
    ) {}

    public function getString(string $key, ?string $default = null): ?string
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (string) $value;
    }

    public function getInt(string $key, ?int $default = null): ?int
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (int) $value;
    }

    public function getFloat(string $key, ?float $default = null): ?float
    {
        $value = $this->data[$key] ?? $default;

        return is_null($value) ? $default : (float) $value;
    }

    public function getArray(string $key, array $default = []): array
    {
        $value = $this->data[$key] ?? $default;

        return empty($value) ? $default : (array) $value;
    }

    public function getEnum(string $key, string $enumClass, $default = null): mixed
    {
        $value = $this->getString($key);

        return ! is_null($value) && enum_exists($enumClass) && method_exists($enumClass, 'tryFrom') ? $enumClass::tryFrom($value) : $default;
    }

    public static function make(array $data): self
    {
        return new self($data);
    }
}
