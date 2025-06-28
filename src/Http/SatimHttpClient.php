<?php

declare(strict_types=1);

namespace LaravelSatim\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use LaravelSatim\Exceptions\SatimApiServerException;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimHttpClient
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class SatimHttpClient
{
    /**
     * @throws SatimApiServerException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function call(string $endpoint, array $data = []): ?array
    {
        try {
            $response = Http::withOptions($this->options())
                ->get($this->getEndpoint($endpoint), $data);

            if ($response->successful() === false) {
                throw new SatimApiServerException("Server error: {$response->reason()} ({$response->status()}).");
            }

            return $response->json();
        } catch (ConnectionException $e) {
            throw new SatimApiServerException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws SatimApiServerException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    protected function getApiUrl(): string
    {
        $apiUrl = config('satim.api_url');
        empty($apiUrl) && throw new SatimApiServerException('SATIM API URL is not configured.');

        return $apiUrl;
    }

    /**
     * @throws SatimApiServerException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    protected function getEndpoint(string $endpoint): string
    {
        return implode('/', [
            rtrim($this->getApiUrl(), '/'),
            ltrim($endpoint, '/'),
        ]);
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    protected function options(): array
    {
        return [
            'verify' => true,
            'allow_redirects' => false,
            'timeout' => config('satim.timeout', 30),
        ];
    }
}
