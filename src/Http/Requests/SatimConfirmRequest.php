<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimValidationException;

final class SatimConfirmRequest implements SatimRequestInterface
{
    /**
     * @throws SatimValidationException
     */
    public function __construct(
        public string $mdOrder,
        public ?SatimLanguage $language = null,
    ) {
        $this->validate();
    }

    /**
     * @throws SatimValidationException
     */
    public static function make(string $mdOrder, ?SatimLanguage $language = null): self
    {
        return new self(
            mdOrder: $mdOrder,
            language: $language
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
        $errors = [];

        if ($this->mdOrder === '') {
            $errors[] = 'The order number is required.';
        } elseif (mb_strlen($this->mdOrder) > 20) {
            $errors[] = 'The order number must not be greater than 20 characters.';
        }

        if ($errors !== []) {
            throw new SatimValidationException($errors[0], $errors);
        }
    }
}
