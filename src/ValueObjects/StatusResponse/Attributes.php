<?php

declare(strict_types=1);

namespace LaravelSatim\ValueObjects\StatusResponse;

final readonly class Attributes
{
    /**
     * @param  array<string, string>  $extra
     */
    public function __construct(
        public ?string $mdOrder = null,
        public array $extra = [],
    ) {
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            mdOrder: $attributes['mdOrder'] ?? null,
            extra: array_diff_key($attributes, array_flip(['mdOrder'])),
        );
    }
}
