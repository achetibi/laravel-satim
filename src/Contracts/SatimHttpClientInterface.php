<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimConnectionException;
use Psr\Http\Message\ResponseInterface;

interface SatimHttpClientInterface
{
    /**
     * @throws SatimConnectionException
     */
    public function send(string $endpoint, SatimRequestInterface $request): ResponseInterface;
}
