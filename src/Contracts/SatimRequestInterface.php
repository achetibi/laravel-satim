<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimValidationException;

interface SatimRequestInterface
{
    /**
     * Business parameters sent to the SATIM gateway (no credentials).
     *
     * @return array<string, mixed>
     */
    public function parameters(): array;

    /**
     * @throws SatimValidationException
     */
    public function validate(): void;
}
