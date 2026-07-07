<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use Psr\Http\Message\ResponseInterface;

interface SatimRequestInterface
{
    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array;

    public function toResponse(ResponseInterface $response): SatimResponseInterface;
}
