<?php

declare(strict_types=1);

namespace LaravelSatim\ValueObjects\StatusResponse;

use LaravelSatim\Support\SatimCaster;

final readonly class CardAuthInfo
{
    public function __construct(
        public ?string $pan = null,
        public ?string $expiration = null,
        public ?string $cardholderName = null,
        public ?string $approvalCode = null,
        public ?string $authorizationResponseId = null,
    ) {
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            pan: SatimCaster::string($data['pan'] ?? null),
            expiration: SatimCaster::string($data['expiration'] ?? null),
            cardholderName: SatimCaster::string($data['cardholderName'] ?? null),
            approvalCode: SatimCaster::string($data['approvalCode'] ?? null),
            authorizationResponseId: SatimCaster::string($data['authorizationResponseId'] ?? null),
        );
    }
}
