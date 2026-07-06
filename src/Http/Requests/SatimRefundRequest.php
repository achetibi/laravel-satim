<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class SatimRefundRequest extends SatimAbstractRequest
{
    public function __construct(
        public string $orderId,
        public float $amount,
    ) {
    }

    public function payload(): array
    {
        return [
            'orderId' => $this->orderId,
            'amount' => (int) round($this->amount * 100),
        ];
    }

    public function rules(): array
    {
        return [
            'orderId' => ['required', 'string', 'alpha_num', 'max:30'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:50', 'max:9999999999.99'],
        ];
    }

    /**
     * @throws SatimResponseException
     */
    public function toResponse(ResponseInterface $response): SatimResponseInterface
    {
        return SatimRefundResponse::fromPsr($response);
    }
}
