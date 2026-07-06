<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

use LaravelSatim\Exceptions\SatimValidationException;

interface SatimValidatorInterface
{
    /**
     * @throws SatimValidationException
     */
    public function validate(SatimRequestInterface $request): void;
}
