<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

final class SatimResponseException extends SatimAbstractException
{
    public function __construct(
        string $message,
        public readonly string $errorCode = 'unknown',
        public readonly string $errorMessage = 'unknown',
    ) {
        parent::__construct($message);
    }

    public static function fromCode(string $errorCode, ?string $errorMessage = null): self
    {
        $key = "satim::exceptions.gateway.{$errorCode}";
        $message = trans()->has($key)
            ? __($key)
            : $errorMessage ?? __('satim::exceptions.gateway.unknown');

        return new self(
            message: $message,
            errorCode: $errorCode,
            errorMessage: $errorMessage
        );
    }

    public static function httpError(int $status, string $reason): self
    {
        return new self(
            message: __('satim::exceptions.http_error', ['status' => $status, 'reason' => $reason]),
            errorCode: (string) $status,
        );
    }

    public static function malformed(): self
    {
        return new self(__('satim::exceptions.malformed_response'));
    }
}
