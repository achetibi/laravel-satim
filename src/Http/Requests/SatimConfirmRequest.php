<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;
use LaravelSatim\Support\SatimValidator;

final class SatimConfirmRequest implements SatimRequestInterface
{
    /**
     * @throws SatimValidationException
     */
    public function __construct(
        public string $mdOrder,
        public ?SatimLanguage $language = null,
    ) {
        $this->mdOrder = trim($this->mdOrder);

        $this->validate();
    }

    /**
     * @throws SatimValidationException
     */
    public static function make(string $mdOrder, ?SatimLanguage $language = null): self
    {
        return new self(
            mdOrder: $mdOrder,
            language: $language,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return [
            'mdOrder' => $this->mdOrder,
            'language' => $this->language?->value,
        ];
    }

    public function validate(): void
    {
        SatimValidator::make()
            ->required($this->mdOrder, 'order id')
            ->token($this->mdOrder, 'order id')
            ->validate();
    }
}
