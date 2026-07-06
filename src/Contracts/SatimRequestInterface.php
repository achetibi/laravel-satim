<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Enums\HttpMethod;
use Psr\Http\Message\ResponseInterface;

interface SatimRequestInterface
{
    public function payload(): array;

    public function rules(): array;

    public function method(): HttpMethod;

    public function toResponse(ResponseInterface $response): SatimResponseInterface;
}
