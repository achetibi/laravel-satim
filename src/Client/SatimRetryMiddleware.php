<?php

declare(strict_types=1);

namespace LaravelSatim\Client;

use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Decides whether a failed gateway call should be retried and how long to wait.
 *
 * Only transport-level failures (connection errors) and server-side 5xx
 * responses are retried; 4xx responses and successful calls are never retried.
 */
final readonly class SatimRetryMiddleware
{
    public function __construct(
        private int $maxRetries,
        private int $delayMs,
    ) {
    }

    public function shouldRetry(
        int $retries,
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ): bool {
        if ($retries >= $this->maxRetries) {
            return false;
        }

        if ($exception instanceof ConnectException) {
            return true;
        }

        return $response !== null && $response->getStatusCode() >= 500;
    }

    /**
     * Linear back-off delay, in milliseconds, before the next attempt.
     */
    public function delay(int $retries): int
    {
        return $this->delayMs * $retries;
    }
}
