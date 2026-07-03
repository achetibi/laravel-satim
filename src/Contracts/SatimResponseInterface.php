<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

interface SatimResponseInterface
{
    /**
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): self;
}
