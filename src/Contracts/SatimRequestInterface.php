<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimInvalidArgumentException;

interface SatimRequestInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * @return array<string, mixed>
     */
    public function toRequest(): array;

    /**
     * @throws SatimInvalidArgumentException
     */
    public function validate(): void;
}
