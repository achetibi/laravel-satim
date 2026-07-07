<?php

declare(strict_types=1);

namespace LaravelSatim\Contracts;

interface SatimResponseInterface
{
    public function successful(): bool;

    /**
     * @return array<string, mixed>
     */
    public function raw(): array;
}
