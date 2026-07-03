<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;

final readonly class SatimRegisterResponse implements SatimResponseInterface
{
    public function __construct(
        public ?string $orderId = null,
        public ?string $formUrl = null,
    ) {
    }

    /**
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): self
    {
        $response ??= [];

        $orderId = $response['orderId'] ?? null;
        $formUrl = $response['formUrl'] ?? null;

        return new self(
            orderId: is_scalar($orderId) ? (string) $orderId : null,
            formUrl: is_scalar($formUrl) ? (string) $formUrl : null,
        );
    }
}
