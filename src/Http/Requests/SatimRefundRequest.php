<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Support\SatimValidator;

final class SatimRefundRequest implements SatimRequestInterface
{
    /**
     * @throws SatimValidationException
     */
    public function __construct(
        public string $orderId,
        public float $amount,
    ) {
        $this->orderId = trim($this->orderId);

        $this->validate();
    }

    /**
     * @throws SatimValidationException
     */
    public static function make(string $orderId, float $amount): self
    {
        return new self(
            orderId: $orderId,
            amount: $amount,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return [
            'orderId' => $this->orderId,
            'amount' => (int) round($this->amount * 100),
        ];
    }

    public function validate(): void
    {
        SatimValidator::make()
            ->required($this->orderId, 'order id')
            ->token($this->orderId, 'order id')
            ->amount($this->amount)
            ->validate();
    }
}
