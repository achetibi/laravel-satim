<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimRegisterRequest
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
final class SatimRegisterRequest extends AbstractSatimRequest implements SatimRequestInterface
{
    /**
     * @throws SatimInvalidArgumentException
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
        public ?SatimLanguage $language = null
    ) {
        $this->validate();
    }

    /**
     * @throws SatimInvalidArgumentException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
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
        ?SatimLanguage $language = null
    ): SatimRegisterRequest {
        return new SatimRegisterRequest(
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
            language: $language
        );
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function toArray(): array
    {
        return [
            'userName' => $this->userName(),
            'password' => $this->password(),
            'orderNumber' => $this->orderNumber,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'returnUrl' => $this->returnUrl,
            'failUrl' => $this->failUrl,
            'description' => $this->description,
            'language' => $this->language,
            'jsonParams' => [
                'force_terminal_id' => $this->terminal(),
                'udf1' => $this->udf1,
                'udf2' => $this->udf2,
                'udf3' => $this->udf3,
                'udf4' => $this->udf4,
                'udf5' => $this->udf5,
            ],
        ];
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function toRequest(): array
    {
        return array_merge($this->toArray(), [
            'amount' => (int) ($this->amount * 100),
            'currency' => $this->currency?->value,
            'language' => $this->language?->value,
            'jsonParams' => json_encode(array_filter($this->toArray()['jsonParams'])),
        ]);
    }

    /**
     * @throws SatimInvalidArgumentException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function validate(): void
    {
        $validator = Validator::make($this->toArray(), [
            'userName' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'max:30'],
            'orderNumber' => ['required', 'string', 'max:10'],
            'amount' => ['required', 'decimal:0,2', 'min:50'],
            'currency' => ['nullable', Rule::enum(SatimCurrency::class)],
            'returnUrl' => ['required', 'url', 'max:512'],
            'failUrl' => ['nullable', 'url', 'max:512'],
            'description' => ['nullable', 'string', 'max:512'],
            'language' => ['nullable', Rule::enum(SatimLanguage::class)],
            'jsonParams' => ['array'],
            'jsonParams.force_terminal_id' => ['required', 'string', 'max:16'],
            'jsonParams.udf1' => ['required', 'string', 'max:20'],
            'jsonParams.udf2' => ['nullable', 'string', 'max:20'],
            'jsonParams.udf3' => ['nullable', 'string', 'max:20'],
            'jsonParams.udf4' => ['nullable', 'string', 'max:20'],
            'jsonParams.udf5' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            throw new SatimInvalidArgumentException($validator->errors()->first());
        }
    }
}
