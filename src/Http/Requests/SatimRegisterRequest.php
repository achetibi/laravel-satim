<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimFundingType;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Support\SatimValidator;

final class SatimRegisterRequest implements SatimRequestInterface
{
    /**
     * @throws SatimValidationException
     */
    public function __construct(
        public string $orderNumber,
        public float $amount,
        public string $returnUrl,
        public string $udf1,
        public ?string $udf2 = null,
        public ?string $udf3 = null,
        public ?string $udf4 = null,
        public ?string $udf5 = null,
        public ?string $failUrl = null,
        public ?string $description = null,
        public ?SatimCurrency $currency = null,
        public ?SatimLanguage $language = null,
        public ?SatimFundingType $fundingType = null,
    ) {
        $this->orderNumber = trim($this->orderNumber);
        $this->returnUrl = trim($this->returnUrl);
        $this->udf1 = trim($this->udf1);
        $this->udf2 = self::clean($this->udf2);
        $this->udf3 = self::clean($this->udf3);
        $this->udf4 = self::clean($this->udf4);
        $this->udf5 = self::clean($this->udf5);
        $this->failUrl = self::clean($this->failUrl);
        $this->description = self::clean($this->description);

        $this->validate();
    }

    /**
     * @throws SatimValidationException
     */
    public static function make(
        string $orderNumber,
        float $amount,
        string $returnUrl,
        string $udf1,
        ?string $udf2 = null,
        ?string $udf3 = null,
        ?string $udf4 = null,
        ?string $udf5 = null,
        ?string $failUrl = null,
        ?string $description = null,
        ?SatimCurrency $currency = null,
        ?SatimLanguage $language = null,
        ?SatimFundingType $fundingType = null,
    ): self {
        return new self(
            orderNumber: $orderNumber,
            amount: $amount,
            returnUrl: $returnUrl,
            udf1: $udf1,
            udf2: $udf2,
            udf3: $udf3,
            udf4: $udf4,
            udf5: $udf5,
            failUrl: $failUrl,
            description: $description,
            currency: $currency,
            language: $language,
            fundingType: $fundingType,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return [
            'orderNumber' => $this->orderNumber,
            'amount' => (int) round($this->amount * 100),
            'currency' => $this->currency?->value,
            'returnUrl' => $this->returnUrl,
            'failUrl' => $this->failUrl,
            'description' => $this->description,
            'language' => $this->language?->value,
            'jsonParams' => array_filter([
                'udf1' => $this->udf1,
                'udf2' => $this->udf2,
                'udf3' => $this->udf3,
                'udf4' => $this->udf4,
                'udf5' => $this->udf5,
                'fundingTypeIndicator' => $this->fundingType?->value,
            ], static fn (?string $value): bool => $value !== null && $value !== ''),
        ];
    }

    public function validate(): void
    {
        SatimValidator::make()
            ->required($this->orderNumber, 'order number')
            ->alphanumeric($this->orderNumber, 'order number', 10)
            ->amount($this->amount)
            ->required($this->returnUrl, 'return URL')
            ->url($this->returnUrl, 'return URL')
            ->url($this->failUrl, 'fail URL')
            ->maxLength($this->description, 'description', 512)
            ->required($this->udf1, 'udf1 field')
            ->alphanumeric($this->udf1, 'udf1 field', 20)
            ->alphanumeric($this->udf2, 'udf2 field', 20)
            ->alphanumeric($this->udf3, 'udf3 field', 20)
            ->alphanumeric($this->udf4, 'udf4 field', 20)
            ->alphanumeric($this->udf5, 'udf5 field', 20)
            ->validate();
    }

    private static function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
