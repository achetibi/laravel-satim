<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use Illuminate\Contracts\Support\MessageBag as MessageBagInterface;
use Illuminate\Support\MessageBag;

final class SatimValidationException extends SatimAbstractException
{
    private function __construct(
        string $message,
        private readonly MessageBagInterface $errors,
    ) {
        parent::__construct($message);
    }

    public static function withErrors(MessageBagInterface $errors): self
    {
        $summary = $errors->first() ?: __('satim::exceptions.validation.failed');

        return new self($summary, $errors);
    }

    public static function make(array $messages): self
    {
        return self::withErrors(new MessageBag($messages));
    }

    public function errors(): MessageBagInterface
    {
        return $this->errors;
    }

    public function messages(): array
    {
        return $this->errors->messages();
    }

    public function first(?string $field = null): ?string
    {
        $message = $field === null ? $this->errors->first() : $this->errors->first($field);

        return $message !== '' ? $message : null;
    }

    public function has(string $field): bool
    {
        return $this->errors->has($field);
    }

    public function toArray(): array
    {
        return $this->messages();
    }
}
