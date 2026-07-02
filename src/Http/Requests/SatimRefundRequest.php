<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Support\Facades\Validator;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

final class SatimRefundRequest extends AbstractSatimRequest implements SatimRequestInterface
{
    /**
     * @throws SatimInvalidArgumentException
     */
    public function __construct(
        public string $orderId,
        public float $amount
    ) {
        $this->validate();
    }

    /**
     * @throws SatimInvalidArgumentException
     */
    public static function make(
        string $orderId,
        float $amount
    ): SatimRefundRequest {
        return new SatimRefundRequest(
            orderId: $orderId,
            amount: $amount
        );
    }

    public function toArray(): array
    {
        return [
            'userName' => $this->userName(),
            'password' => $this->password(),
            'orderId' => $this->orderId,
            'amount' => $this->amount,
        ];
    }

    public function toRequest(): array
    {
        return array_merge($this->toArray(), [
            'amount' => (int) ($this->amount * 100),
        ]);
    }

    /**
     * @throws SatimInvalidArgumentException
     */
    public function validate(): void
    {
        $validator = Validator::make($this->toArray(), [
            'userName' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'max:30'],
            'orderId' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'decimal:0,2', 'min:50'],
        ]);

        if ($validator->fails()) {
            throw new SatimInvalidArgumentException($validator->errors()->first());
        }
    }
}
