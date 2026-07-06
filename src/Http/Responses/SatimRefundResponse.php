<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

final readonly class SatimRefundResponse extends SatimAbstractResponse
{
    public function successful(): bool
    {
        return $this->errorCode() === 0;
    }

    public function errorCode(): ?int
    {
        $value = $this->data['errorCode'] ?? $this->data['ErrorCode'] ?? null;

        return is_int($value) ? $value : null;
    }

    public function errorMessage(): ?string
    {
        $value = $this->data['errorMessage'] ?? $this->data['ErrorMessage'] ?? null;

        return is_string($value) ? $value : null;
    }
}
