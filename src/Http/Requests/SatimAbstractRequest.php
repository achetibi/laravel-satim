<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;

abstract readonly class SatimAbstractRequest implements SatimRequestInterface
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Recursively remove null values (and the empty arrays they leave behind)
     * so optional fields are never sent to the gateway.
     *
     * @template TKey of array-key
     *
     * @param  array<TKey, mixed>  $payload
     * @return array<TKey, mixed>
     */
    protected function clean(array $payload): array
    {
        $cleaned = [];

        foreach ($payload as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->clean($value);

                if ($value === []) {
                    continue;
                }
            }

            $cleaned[$key] = $value;
        }

        return $cleaned;
    }
}
