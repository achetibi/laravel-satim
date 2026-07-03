<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;

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
    ) {
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
            ], static fn (?string $value): bool => $value !== null && $value !== ''),
        ];
    }

    public function validate(): void
    {
        $errors = [];

        if ($this->orderNumber === '') {
            $errors[] = 'The order number is required.';
        } elseif (mb_strlen($this->orderNumber) > 10) {
            $errors[] = 'The order number must not be greater than 10 characters.';
        }

        if ($this->amount < 50) {
            $errors[] = 'The amount must be at least 50.';
        } elseif (round($this->amount, 2) !== $this->amount) {
            $errors[] = 'The amount must not have more than two decimal places.';
        }

        if ($this->returnUrl === '') {
            $errors[] = 'The return URL is required.';
        } elseif (filter_var($this->returnUrl, FILTER_VALIDATE_URL) === false) {
            $errors[] = 'The return URL must be a valid URL.';
        } elseif (mb_strlen($this->returnUrl) > 512) {
            $errors[] = 'The return URL must not be greater than 512 characters.';
        }

        if ($this->failUrl !== null) {
            if (filter_var($this->failUrl, FILTER_VALIDATE_URL) === false) {
                $errors[] = 'The fail URL must be a valid URL.';
            } elseif (mb_strlen($this->failUrl) > 512) {
                $errors[] = 'The fail URL must not be greater than 512 characters.';
            }
        }

        if ($this->description !== null && mb_strlen($this->description) > 512) {
            $errors[] = 'The description must not be greater than 512 characters.';
        }

        if ($this->udf1 === '') {
            $errors[] = 'The udf1 field is required.';
        }

        foreach (['udf1' => $this->udf1, 'udf2' => $this->udf2, 'udf3' => $this->udf3, 'udf4' => $this->udf4, 'udf5' => $this->udf5] as $name => $value) {
            if ($value !== null && mb_strlen($value) > 20) {
                $errors[] = "The {$name} field must not be greater than 20 characters.";
            }
        }

        if ($errors !== []) {
            throw new SatimValidationException($errors[0], $errors);
        }
    }
}
