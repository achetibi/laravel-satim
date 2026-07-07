<?php

declare(strict_types=1);

namespace LaravelSatim\Client;

use Illuminate\Support\Facades\Log;
use JsonException;
use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\HttpMethod;
use LaravelSatim\Exceptions\SatimConfigurationException;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Exceptions\SatimEncodingException;
use LaravelSatim\Support\SatimConfig;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class SatimHttpClient implements SatimHttpClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private SatimConfig $config,
    ) {
    }

    /**
     * @throws SatimConfigurationException
     * @throws SatimConnectionException
     * @throws SatimEncodingException
     */
    public function send(string $endpoint, SatimRequestInterface $request): ResponseInterface
    {
        $method = $this->config->httpMethod();
        $uri = $this->config->baseUrl() . '/' . ltrim($endpoint, '/');
        $payload = $this->preparePayload($request);

        if ($method === HttpMethod::GET) {
            $uri .= '?' . http_build_query($payload);
        }

        $psr = $this->requestFactory->createRequest($method->value, $uri)
            ->withHeader('Accept', 'application/json');

        if ($method === HttpMethod::POST) {
            $psr = $psr->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($this->streamFactory->createStream(http_build_query($payload)));
        }

        $this->log('SATIM request', ['endpoint' => $endpoint, 'method' => $method->value]);

        try {
            $response = $this->client->sendRequest($psr);
        } catch (ClientExceptionInterface $e) {
            $this->log('SATIM connection failed', ['endpoint' => $endpoint, 'reason' => $e->getMessage()]);

            throw SatimConnectionException::from($e);
        }

        $this->log('SATIM response', ['endpoint' => $endpoint, 'status' => $response->getStatusCode()]);

        return $response;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SatimConfigurationException
     * @throws SatimEncodingException
     */
    private function preparePayload(SatimRequestInterface $request): array
    {
        $payload = array_merge($this->config->credentials(), $request->payload());

        if (isset($payload['jsonParams']) && is_array($payload['jsonParams'])) {
            if ($this->config->terminalId() !== null) {
                $payload['jsonParams']['force_terminal_id'] = $this->config->terminalId();
            }

            try {
                $payload['jsonParams'] = json_encode($payload['jsonParams'], JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw SatimEncodingException::forJsonParams($e);
            }
        }

        return $payload;
    }

    /**
     * Log a gateway interaction when logging is enabled. Sensitive payload data
     * (credentials, card details) is intentionally never logged.
     *
     * @param  array<string, mixed>  $context
     */
    private function log(string $message, array $context): void
    {
        if (! $this->config->loggingEnabled()) {
            return;
        }

        Log::channel($this->config->logChannel())->debug($message, $context);
    }
}
