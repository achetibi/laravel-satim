<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimConfirmRequest
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
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
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
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
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
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
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function toRequest(): array
    {
        return array_merge($this->toArray(), [
            'language' => $this->language?->value,
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
            'orderId' => ['required', 'string', 'max:20'],
            'language' => ['nullable', Rule::enum(SatimLanguage::class)],
        ]);

        if ($validator->fails()) {
            throw new SatimInvalidArgumentException($validator->errors()->first());
        }
    }
}
