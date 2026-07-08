<?php

declare(strict_types=1);

namespace LaravelSatim\ValueObjects\StatusResponse;

use DateTimeImmutable;
use LaravelSatim\Support\SatimCaster;

final readonly class MerchantOrderParams
{
    /**
     * @param  array<string, string>  $extra
     */
    public function __construct(
        public ?bool $disablePhone = null,
        public ?bool $disableEmail = null,
        public ?string $forceTerminalId = null,
        public ?string $udf1 = null,
        public ?string $udf2 = null,
        public ?string $udf3 = null,
        public ?string $udf4 = null,
        public ?string $udf5 = null,
        public ?DateTimeImmutable $transmissionDate = null,
        public array $extra = [],
    ) {
    }

    /**
     * @param  array<string, string>  $params
     */
    public static function fromArray(array $params): self
    {
        $known = [
            'disablePhone', 'disableEmail', 'force_terminal_id',
            'udf1', 'udf2', 'udf3', 'udf4', 'udf5', 'transmissionDate',
        ];

        return new self(
            disablePhone: SatimCaster::boolean($params['disablePhone'] ?? null),
            disableEmail: SatimCaster::boolean($params['disableEmail'] ?? null),
            forceTerminalId: $params['force_terminal_id'] ?? null,
            udf1: $params['udf1'] ?? null,
            udf2: $params['udf2'] ?? null,
            udf3: $params['udf3'] ?? null,
            udf4: $params['udf4'] ?? null,
            udf5: $params['udf5'] ?? null,
            transmissionDate: SatimCaster::dateTime($params['transmissionDate'] ?? null),
            extra: array_diff_key($params, array_flip($known)),
        );
    }
}
