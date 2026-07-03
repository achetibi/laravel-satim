<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

class SatimValidationException extends SatimException
{
    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(string $message = '', private readonly array $errors = [])
    {
        parent::__construct($message);
    }

    /**
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
