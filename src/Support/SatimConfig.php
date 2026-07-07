<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Environment;
use LaravelSatim\Enums\HttpMethod;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimConfigurationException;

final readonly class SatimConfig
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private array $config
    ) {
    }

    /**
     * @throws SatimConfigurationException
     */
    public function environment(): Environment
    {
        $value = $this->getString('environment', Environment::TEST->value);

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
     * @return array{userName: string, password: string}
     *
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
        $value = $this->get('credentials.terminal_id');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * HTTP method used for every gateway call.
     *
     * SATIM strongly recommends POST so credentials travel in the request body
     * instead of being exposed in the URL, query string, proxy logs or history.
     *
     * @throws SatimConfigurationException
     */
    public function httpMethod(): HttpMethod
    {
        $value = strtoupper($this->getString('http.method', HttpMethod::POST->value));

        if ($value === '') {
            return HttpMethod::POST;
        }

        return HttpMethod::tryFrom($value) ?? throw SatimConfigurationException::invalidValue('http.method', $value);
    }

    public function retries(): int
    {
        return max(0, $this->getInt('http.retries'));
    }

    public function retryDelay(): int
    {
        return max(0, $this->getInt('http.retry_delay', 300));
    }

    /**
     * @return array<string, mixed>
     */
    public function guzzleOptions(): array
    {
        $options = $this->get('http.options', []);

        /** @var array<string, mixed> $extra */
        $extra = is_array($options) ? $options : [];

        return array_merge([
            'timeout' => $this->getFloat('http.timeout', 30),
            'connect_timeout' => $this->getFloat('http.connect_timeout', 10),
            'verify' => $this->getBool('http.verify', true),
            'http_errors' => false,
        ], $extra);
    }

    /**
     * @throws SatimConfigurationException
     */
    public function defaultCurrency(): Currency
    {
        $value = $this->getString('defaults.currency', Currency::DZD->value);

        return Currency::tryFrom($value) ?? throw SatimConfigurationException::invalidValue('defaults.currency', $value);
    }

    /**
     * @throws SatimConfigurationException
     */
    public function defaultLanguage(): Language
    {
        $value = $this->getString('defaults.language', Language::ENGLISH->value);

        return Language::tryFrom($value) ?? throw SatimConfigurationException::invalidValue('defaults.language', $value);
    }

    public function loggingEnabled(): bool
    {
        return $this->getBool('logging.enabled', false);
    }

    public function logChannel(): string
    {
        return $this->getString('logging.channel', 'stack');
    }

    private function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    private function getString(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);

        return is_scalar($value) ? (string) $value : $default;
    }

    private function getInt(string $key, int $default = 0): int
    {
        $value = $this->get($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function getFloat(string $key, float $default): float
    {
        $value = $this->get($key, $default);

        return is_numeric($value) ? (float) $value : $default;
    }

    private function getBool(string $key, bool $default): bool
    {
        return (bool) $this->get($key, $default);
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
