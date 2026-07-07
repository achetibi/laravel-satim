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
        return $this->integer('errorCode', 'ErrorCode');
    }

    public function errorMessage(): ?string
    {
        return $this->string('errorMessage', 'ErrorMessage');
    }
}
