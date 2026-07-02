<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimInvalidArgumentException;

interface SatimRequestInterface
{
    public function toArray(): array;

    public function toRequest(): array;

    /**
     * @throws SatimInvalidArgumentException
     */
    public function validate(): void;
}
