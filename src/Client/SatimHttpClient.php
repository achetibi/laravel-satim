<?php

declare(strict_types=1);

namespace LaravelSatim\Client;

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
        $uri = rtrim($this->config->baseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $payload = $this->preparePayload($request);

        if ($request->method() === HttpMethod::GET) {
            $uri .= '?'.http_build_query($payload);
        }

        $psr = $this->requestFactory->createRequest($request->method()->value, $uri)
            ->withHeader('Accept', 'application/json');

        if ($request->method() === HttpMethod::POST) {
            $psr = $psr->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($this->streamFactory->createStream(http_build_query($payload)));
        }

        try {
            return $this->client->sendRequest($psr);
        } catch (ClientExceptionInterface $e) {
            throw SatimConnectionException::from($e);
        }
    }

    /**
     * @throws SatimEncodingException
     */
    private function preparePayload(SatimRequestInterface $request): array
    {
        $payload = array_merge($this->config->credentials(), $request->payload());

        if (isset($payload['jsonParams']) && is_array($payload['jsonParams'])) {
            $payload['jsonParams']['force_terminal_id'] = $this->config->terminalId();

            try {
                $payload['jsonParams'] = json_encode($payload['jsonParams'], JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw SatimEncodingException::forJsonParams($e);
            }
        }

        return $payload;
    }
}
