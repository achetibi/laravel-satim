<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\HttpMethod;

abstract readonly class SatimAbstractRequest implements SatimRequestInterface
{
    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function rules(): array
    {
        return [];
    }

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
