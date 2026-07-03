<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use Exception;
use Throwable;

abstract class SatimException extends Exception
{
    /**
     * @param  array<array-key, mixed>  $context
     */
    public function __construct(
        string $message = '',
        protected int $satimErrorCode = 0,
        protected array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function errorCode(): int
    {
        return $this->satimErrorCode;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }
}
