<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use Illuminate\Support\Facades\Lang;
use LaravelSatim\Enums\Language;

/**
 * Resolves package translations against a supported locale.
 *
 * The package only ships translations for the locales backed by the
 * {@see Language} enum (ar, en, fr). When the active Laravel locale is not one
 * of them, translations are resolved against the package fallback locale
 * (config "satim.defaults.language") instead of returning raw translation keys.
 */
final class SatimTranslator
{
    /**
     * Translate a package key using the resolved locale.
     *
     * @param  array<string, int|float|string>  $replace
     */
    public static function get(string $key, array $replace = []): string
    {
        $line = trans($key, $replace, self::locale());

        return is_string($line) ? $line : $key;
    }

    /**
     * Fetch a group of translation lines (e.g. validation messages).
     *
     * @return array<string, mixed>
     */
    public static function group(string $key): array
    {
        $lines = trans($key, [], self::locale());

        if (! is_array($lines)) {
            return [];
        }

        /** @var array<string, mixed> $lines */
        return $lines;
    }

    /**
     * Determine whether a package key exists for the resolved locale.
     */
    public static function has(string $key): bool
    {
        return Lang::has($key, self::locale());
    }

    /**
     * Resolve the locale used to read package translations.
     *
     * Falls back to the configured package default locale, and ultimately to
     * English, when the active application locale is not supported.
     */
    public static function locale(): string
    {
        $supported = self::supported();
        $current = (string) app()->getLocale();

        if (in_array($current, $supported, true)) {
            return $current;
        }

        $fallback = config('satim.defaults.language', Language::ENGLISH->value);

        return is_string($fallback) && in_array($fallback, $supported, true)
            ? $fallback
            : Language::ENGLISH->value;
    }

    /**
     * The locales for which the package ships translations.
     *
     * @return list<string>
     */
    public static function supported(): array
    {
        return array_map(static fn (Language $language): string => $language->value, Language::cases());
    }
}
