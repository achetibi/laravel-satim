<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Validation\Rule;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimStatusResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class SatimStatusRequest extends SatimAbstractRequest
{
    public function __construct(
        public string $orderId,
        public ?Language $language = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'orderId' => $this->orderId,
            'language' => Language::withFallback($this->language)->code(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'orderId' => ['required', 'string', 'alpha_num', 'max:30'],
            'language' => ['nullable', Rule::enum(Language::class)],
        ];
    }

    /**
     * @throws SatimResponseException
     */
    public function toResponse(ResponseInterface $response): SatimStatusResponse
    {
        return SatimStatusResponse::fromPsr($response);
    }
}
