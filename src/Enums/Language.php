<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

use LaravelSatim\Support\SatimConfig;

enum Language: string
{
    case ARABIC = 'ar';
    case ENGLISH = 'en';
    case FRENCH = 'fr';

    public function code(): string
    {
        return match ($this) {
            self::ARABIC => 'ar',
            self::ENGLISH => 'en',
            self::FRENCH => 'fr',
        };
    }

    public static function withFallback(?self $language): self
    {
        return $language ?? app(SatimConfig::class)->defaultLanguage();
    }
}
