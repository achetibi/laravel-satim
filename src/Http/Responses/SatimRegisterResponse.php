<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

final readonly class SatimRegisterResponse extends SatimAbstractResponse
{
    public function successful(): bool
    {
        return $this->errorCode() === 0 && ! empty($this->formUrl());
    }

    public function errorCode(): int
    {
        $value = $this->data['errorCode'] ?? $this->data['ErrorCode'] ?? null;

        return is_int($value) ? $value : 0;
    }

    public function errorMessage(): ?string
    {
        $value = $this->data['errorMessage'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function formUrl(): ?string
    {
        $value = $this->data['formUrl'] ?? null;

        return is_string($value) ? $value : null;
    }
}
