<?php

declare(strict_types=1);

namespace LaravelSatim\Http;

use Illuminate\Support\Facades\Http;
use LaravelSatim\Exceptions\SatimApiServerException;
use Throwable;

class SatimHttpClient
{
    /**
     * @throws SatimApiServerException
     */
    public function call(string $endpoint, array $data = []): ?array
    {
        try {
            $retry = (int) config('satim.http_client.retry', 0);
            $sleeptime = (int) config('satim.http_client.sleeptime', 1);

            $response = Http::withOptions($this->options())
                ->when($retry > 0, fn ($http) => $http->retry($retry, $sleeptime))
                ->get($this->getEndpoint($endpoint), $data);

            if ($response->successful() === false) {
                throw new SatimApiServerException("Server Error: {$response->reason()} ({$response->status()}).");
            }

            return $response->json();
        } catch (Throwable $e) {
            throw new SatimApiServerException($e->getMessage());
        }
    }

    /**
     * @throws SatimApiServerException
     */
    protected function getApiUrl(): string
    {
        $apiUrl = config('satim.api_url');
        empty($apiUrl) && throw new SatimApiServerException('SATIM API URL is not configured.');

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

    protected function options(): array
    {
        return [
            'verify' => config('satim.http_options.verify', true),
            'allow_redirects' => config('satim.http_options.allow_redirects', false),
            'timeout' => (int) config('satim.http_options.timeout', 30),
        ];
    }
}
