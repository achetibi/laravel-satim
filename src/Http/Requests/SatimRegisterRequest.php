<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Validation\Rule;
use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\FundingType;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class SatimRegisterRequest extends SatimAbstractRequest
{
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
        public ?Currency $currency = null,
        public ?Language $language = null,
        public ?FundingType $fundingType = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->clean([
            'orderNumber' => $this->orderNumber,
            'amount' => (int) round($this->amount * 100),
            'returnUrl' => $this->returnUrl,
            'currency' => Currency::withFallback($this->currency)->code(),
            'language' => Language::withFallback($this->language)->code(),
            'failUrl' => $this->failUrl,
            'description' => $this->description,
            'jsonParams' => [
                'udf1' => $this->udf1,
                'udf2' => $this->udf2,
                'udf3' => $this->udf3,
                'udf4' => $this->udf4,
                'udf5' => $this->udf5,
                'fundingTypeIndicator' => $this->fundingType?->value,
            ]
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'orderNumber' => ['required', 'string', 'alpha_num', 'max:10'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:50', 'max:9999999999.99'],
            'returnUrl' => ['required', 'url', 'max:512'],
            'failUrl' => ['nullable', 'url', 'max:512'],
            'description' => ['nullable', 'string', 'alpha_num', 'max:512'],
            'currency' => ['nullable', Rule::enum(Currency::class)],
            'language' => ['nullable', Rule::enum(Language::class)],
            'udf1' => ['required', 'string', 'alpha_num', 'max:20'],
            'udf2' => ['nullable', 'string', 'alpha_num', 'max:20'],
            'udf3' => ['nullable', 'string', 'alpha_num', 'max:20'],
            'udf4' => ['nullable', 'string', 'alpha_num', 'max:20'],
            'udf5' => ['nullable', 'string', 'alpha_num', 'max:20'],
            'fundingType' => ['nullable', Rule::enum(FundingType::class)],
        ];
    }

    /**
     * @throws SatimResponseException
     */
    public function toResponse(ResponseInterface $response): SatimRegisterResponse
    {
        return SatimRegisterResponse::fromPsr($response);
    }
}
