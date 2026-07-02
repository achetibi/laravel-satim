<?php

declare(strict_types=1);

namespace LaravelSatim\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LaravelSatim\Exceptions\SatimApiServerException;
use Throwable;

class SatimHttpClient
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<array-key, mixed>|null
     *
     * @throws SatimApiServerException
     */
    public function call(string $endpoint, array $data = []): ?array
    {
        try {
            $retry = $this->intConfig('satim.http_client.retry', 0);
            $sleeptime = $this->intConfig('satim.http_client.sleeptime', 1);
            $method = strtolower($this->stringConfig('satim.http_client.method', 'POST'));

            $request = Http::withOptions($this->options())
                ->when($retry > 0, fn (PendingRequest $http) => $http->retry($retry, $sleeptime));

            $response = $method === 'get'
                ? $request->get($this->getEndpoint($endpoint), $data)
                : $request->asForm()->post($this->getEndpoint($endpoint), $data);

            if ($response->successful() === false) {
                throw new SatimApiServerException("Server Error: {$response->reason()} ({$response->status()}).");
            }

            $json = $response->json();

            return is_array($json) ? $json : null;
        } catch (Throwable $e) {
            throw new SatimApiServerException($e->getMessage());
        }
    }

    /**
     * @throws SatimApiServerException
     */
    protected function getApiUrl(): string
    {
        $apiUrl = $this->stringConfig('satim.api_url', '');
        $apiUrl === '' && throw new SatimApiServerException('SATIM API URL is not configured.');

        return $apiUrl;
    }

    /**
     * @throws SatimApiServerException
     */
    protected function getEndpoint(string $endpoint): string
    {
        return implode('/', [
            rtrim($this->getApiUrl(), '/'),
            ltrim($endpoint, '/'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function options(): array
    {
        return [
            'verify' => config('satim.http_options.verify', true),
            'allow_redirects' => config('satim.http_options.allow_redirects', false),
            'timeout' => $this->intConfig('satim.http_options.timeout', 30),
        ];
    }

    private function intConfig(string $key, int $default): int
    {
        $value = config($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function stringConfig(string $key, string $default): string
    {
        $value = config($key, $default);

        return is_string($value) ? $value : $default;
    }
}
