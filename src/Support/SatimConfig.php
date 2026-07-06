<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Environment;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimConfigurationException;

final readonly class SatimConfig
{
    public function __construct(
        private array $config
    ) {
    }

    /**
     * @throws SatimConfigurationException
     */
    public function environment(): Environment
    {
        $value = (string) $this->get('environment', Environment::TEST->value);

        return Environment::tryFrom($value) ?? throw SatimConfigurationException::invalidEnvironment($value);
    }

    /**
     * @throws SatimConfigurationException
     */
    public function baseUrl(): string
    {
        $env = $this->environment()->value;
        $url = $this->get("base_urls.{$env}");

        if (! is_string($url) || $url === '') {
            throw SatimConfigurationException::missingBaseUrl($env);
        }

        return rtrim($url, '/');
    }

    /**
     * @throws SatimConfigurationException
     */
    public function credentials(): array
    {
        return [
            'userName' => $this->mustGet('credentials.username'),
            'password' => $this->mustGet('credentials.password'),
        ];
    }

    public function terminalId(): ?string
    {
        return $this->mustGet('credentials.terminal_id');
    }

    public function httpClient(): ?string
    {
        $client = $this->get('http.client');

        return is_string($client) && $client !== '' ? $client : null;
    }

    public function retries(): int
    {
        return max(0, (int) $this->get('http.retries', 0));
    }

    public function guzzleOptions(): array
    {
        $extra = (array) $this->get('http.options', []);

        return array_merge([
            'timeout' => (float) $this->get('http.timeout', 30),
            'connect_timeout' => (float) $this->get('http.connect_timeout', 10),
            'verify' => (bool) $this->get('http.verify', true),
            'http_errors' => false,
        ], $extra);
    }

    /**
     * @throws SatimConfigurationException
     */
    public function defaultCurrency(): Currency
    {
        $value = (string) $this->get('defaults.currency', Currency::DZD->value);

        return Currency::tryFrom($value) ?? throw SatimConfigurationException::invalidValue('defaults.currency', $value);
    }

    /**
     * @throws SatimConfigurationException
     */
    public function defaultLanguage(): Language
    {
        $value = (string) $this->get('defaults.language', Language::ENGLISH->value);

        return Language::tryFrom($value) ?? throw SatimConfigurationException::invalidValue('defaults.language', $value);
    }

    public function loggingEnabled(): bool
    {
        return (bool) $this->get('logging.enabled', false);
    }

    public function logChannel(): string
    {
        return (string) $this->get('logging.channel', 'stack');
    }

    private function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * @throws SatimConfigurationException
     */
    private function mustGet(string $key): string
    {
        $value = $this->get($key);

        if (! is_string($value) || $value === '') {
            throw SatimConfigurationException::missing($key);
        }

        return $value;
    }
}
