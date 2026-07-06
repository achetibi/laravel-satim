<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Support\SatimConfig;

enum Currency: string
{
    case DZD = 'DZD';

    public function code(): string
    {
        return match ($this) {
            self::DZD => '012',
        };
    }

    public static function fromCode(string $code): self
    {
        return collect(self::cases())->firstWhere(fn (self $case) => $case->code() === $code) ?? self::withFallback(self::DZD);
    }

    public static function withFallback(?self $currency): self
    {
        return $currency ?? app(SatimConfig::class)->defaultCurrency();
    }
}
