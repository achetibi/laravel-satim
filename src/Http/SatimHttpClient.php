<?php

declare(strict_types=1);

namespace LaravelSatim\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Support\SatimCredentials;
use Throwable;

readonly class SatimHttpClient
{
    /**
     * @param  array<string, mixed>  $httpOptions
     */
    public function __construct(
        private SatimCredentials $credentials,
        private string $baseUrl,
        private SatimCurrency $defaultCurrency,
        private SatimLanguage $defaultLanguage,
        private string $method = 'POST',
        private int $retry = 0,
        private int $retryDelay = 1,
        private array $httpOptions = [],
    ) {
    }

    /**
     * @return array<array-key, mixed>
     *
     * @throws SatimConnectionException
     */
    public function send(string $endpoint, SatimRequestInterface $request): array
    {
        $payload = $this->assemble($request->parameters());

        try {
            $http = Http::withOptions($this->httpOptions)
                ->when($this->retry > 0, fn (PendingRequest $r) => $r->retry($this->retry, $this->retryDelay));

            $response = strtolower($this->method) === 'get'
                ? $http->get($this->url($endpoint), $payload)
                : $http->asForm()->post($this->url($endpoint), $payload);
        } catch (Throwable $e) {
            throw new SatimConnectionException($e->getMessage(), 0, [], $e);
        }

        if ($response->successful() === false) {
            throw new SatimConnectionException("Server Error: {$response->reason()} ({$response->status()}).");
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function assemble(array $parameters): array
    {
        $parameters['userName'] = $this->credentials->userName;
        $parameters['password'] = $this->credentials->password;

        if (array_key_exists('currency', $parameters) && ($parameters['currency'] === null || $parameters['currency'] === '')) {
            $parameters['currency'] = $this->defaultCurrency->value;
        }

        if (array_key_exists('language', $parameters) && ($parameters['language'] === null || $parameters['language'] === '')) {
            $parameters['language'] = $this->defaultLanguage->value;
        }

        if (array_key_exists('jsonParams', $parameters) && is_array($parameters['jsonParams'])) {
            $jsonParams = $parameters['jsonParams'];
            $jsonParams['force_terminal_id'] = $this->credentials->terminal;
            $parameters['jsonParams'] = json_encode($jsonParams);
        }

        return $parameters;
    }

    private function url(string $endpoint): string
    {
        return implode('/', [rtrim($this->baseUrl, '/'), ltrim($endpoint, '/')]);
    }
}
