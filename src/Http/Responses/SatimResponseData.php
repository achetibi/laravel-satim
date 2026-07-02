<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use BackedEnum;
use Illuminate\Support\Arr;

final readonly class SatimResponseData
{
    public function __construct(private array $attributes = [])
    {
    }

    public static function from(?array $attributes): self
    {
        return new self($attributes ?? []);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->attributes, $key);
    }

    public function string(string $key, ?string $default = null): ?string
    {
        $value = Arr::get($this->attributes, $key);

        return $value === null ? $default : (string) $value;
    }

    public function integer(string $key, ?int $default = null): ?int
    {
        $value = Arr::get($this->attributes, $key);

        return $value === null ? $default : (int) $value;
    }

    public function float(string $key, ?float $default = null): ?float
    {
        $value = Arr::get($this->attributes, $key);

        return $value === null ? $default : (float) $value;
    }

    public function array(string $key, array $default = []): array
    {
        $value = Arr::get($this->attributes, $key);

        return empty($value) ? $default : (array) $value;
    }

    public function enum(string $key, string $enum, ?BackedEnum $default = null): ?BackedEnum
    {
        $value = $this->string($key);

        return $value === null ? $default : ($enum::tryFrom($value) ?? $default);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
