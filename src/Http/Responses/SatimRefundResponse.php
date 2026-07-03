<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;

final readonly class SatimRefundResponse implements SatimResponseInterface
{
    public function __construct(
        public ?string $errorMessage = null,
    ) {
    }

    /**
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): self
    {
        $response ??= [];

        $errorMessage = $response['errorMessage'] ?? null;

        return new self(
            errorMessage: is_scalar($errorMessage) ? (string) $errorMessage : null,
        );
    }
}
