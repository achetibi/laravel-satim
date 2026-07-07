<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

final readonly class SatimRegisterResponse extends SatimAbstractResponse
{
    public function successful(): bool
    {
        return $this->errorCode() === 0 && ($this->formUrl() ?? '') !== '';
    }

    public function errorCode(): int
    {
        return $this->integer('errorCode', 'ErrorCode') ?? 0;
    }

    public function errorMessage(): ?string
    {
        return $this->string('errorMessage', 'ErrorMessage');
    }

    public function orderId(): ?string
    {
        return $this->string('orderId');
    }

    public function formUrl(): ?string
    {
        return $this->string('formUrl');
    }
}
