<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

final class SatimConfirmRequest extends AbstractSatimRequest implements SatimRequestInterface
{
    /**
     * @throws SatimInvalidArgumentException
     */
    public function __construct(
        public string $orderId,
        public ?SatimLanguage $language = null
    ) {
        $this->validate();
    }

    /**
     * @throws SatimInvalidArgumentException
     */
    public static function make(
        string $orderId,
        ?SatimLanguage $language = null
    ): SatimConfirmRequest {
        return new SatimConfirmRequest(
            orderId: $orderId,
            language: $language
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'userName' => $this->userName(),
            'password' => $this->password(),
            'orderId' => $this->orderId,
            'language' => $this->language,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequest(): array
    {
        return [
            'userName' => $this->userName(),
            'password' => $this->password(),
            'mdOrder' => $this->orderId,
            'language' => $this->language?->value,
        ];
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
            'language' => ['nullable', Rule::enum(SatimLanguage::class)],
        ]);

        if ($validator->fails()) {
            throw new SatimInvalidArgumentException($validator->errors()->first());
        }
    }
}
