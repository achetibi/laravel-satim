<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimValidationException;

final class SatimRefundRequest implements SatimRequestInterface
{
    /**
     * @throws SatimValidationException
     */
    public function __construct(
        public string $orderId,
        public float $amount,
    ) {
        $this->validate();
    }

    /**
     * @throws SatimValidationException
     */
    public static function make(string $orderId, float $amount): self
    {
        return new self(orderId: $orderId, amount: $amount);
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
        $errors = [];

        if ($this->orderId === '') {
            $errors[] = 'The order id is required.';
        } elseif (mb_strlen($this->orderId) > 20) {
            $errors[] = 'The order id must not be greater than 20 characters.';
        }

        if ($this->amount < 50) {
            $errors[] = 'The amount must be at least 50.';
        } elseif (round($this->amount, 2) !== $this->amount) {
            $errors[] = 'The amount must not have more than two decimal places.';
        }

        if ($errors !== []) {
            throw new SatimValidationException($errors[0], $errors);
        }
    }
}
