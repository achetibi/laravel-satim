<?php

declare(strict_types=1);

namespace LaravelSatim\ValueObjects\ConfirmResponse;

use LaravelSatim\Support\SatimCaster;

final readonly class Params
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public ?string $respCode = null,
        public ?string $respCodeDesc = null,
        public ?string $udf1 = null,
        public ?string $udf2 = null,
        public ?string $udf3 = null,
        public ?string $udf4 = null,
        public ?string $udf5 = null,
        public array $extra = [],
    ) {
    }

    /**
     * @param  array<array-key, mixed>  $params
     */
    public static function fromArray(array $params): self
    {
        $known = ['respCode', 'respCode_desc', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5'];

        $extra = [];

        foreach (array_diff_key($params, array_flip($known)) as $key => $value) {
            $extra[(string) $key] = $value;
        }

        return new self(
            respCode: SatimCaster::string($params['respCode'] ?? null),
            respCodeDesc: SatimCaster::string($params['respCode_desc'] ?? null),
            udf1: SatimCaster::string($params['udf1'] ?? null),
            udf2: SatimCaster::string($params['udf2'] ?? null),
            udf3: SatimCaster::string($params['udf3'] ?? null),
            udf4: SatimCaster::string($params['udf4'] ?? null),
            udf5: SatimCaster::string($params['udf5'] ?? null),
            extra: $extra,
        );
    }
}
