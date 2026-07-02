<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use BackedEnum;
use Illuminate\Support\Arr;

final readonly class SatimResponseData
{
    /**
     * @param  array<array-key, mixed>  $attributes
     */
    public function __construct(private array $attributes = [])
    {
    }

    /**
     * @param  array<array-key, mixed>|null  $attributes
     */
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

        return is_scalar($value) ? (string) $value : $default;
    }

    public function integer(string $key, ?int $default = null): ?int
    {
        $value = Arr::get($this->attributes, $key);

        return is_scalar($value) ? (int) $value : $default;
    }

    public function float(string $key, ?float $default = null): ?float
    {
        $value = Arr::get($this->attributes, $key);

        return is_scalar($value) ? (float) $value : $default;
    }

    /**
     * @param  array<array-key, mixed>  $default
     * @return array<array-key, mixed>
     */
    public function array(string $key, array $default = []): array
    {
        $value = Arr::get($this->attributes, $key);

        return is_array($value) && $value !== [] ? $value : $default;
    }

    /**
     * @template TEnum of BackedEnum
     *
     * @param  class-string<TEnum>  $enum
     * @param  TEnum|null  $default
     * @return TEnum|null
     */
    public function enum(string $key, string $enum, ?BackedEnum $default = null): ?BackedEnum
    {
        $value = $this->string($key);

        return $value === null ? $default : ($enum::tryFrom($value) ?? $default);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
