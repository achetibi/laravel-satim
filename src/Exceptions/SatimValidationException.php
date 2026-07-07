<?php

declare(strict_types=1);

namespace LaravelSatim\Exceptions;

use Illuminate\Support\MessageBag;
use LaravelSatim\Support\SatimTranslator;

final class SatimValidationException extends SatimAbstractException
{
    private function __construct(
        string $message,
        private readonly MessageBag $errors,
    ) {
        parent::__construct($message);
    }

    public static function withErrors(MessageBag $errors): self
    {
        $summary = $errors->first() ?: SatimTranslator::get('satim::exceptions.validation.failed');

        return new self($summary, $errors);
    }

    /**
     * @param  array<string, string|array<int, string>>  $messages
     */
    public static function make(array $messages): self
    {
        return self::withErrors(new MessageBag($messages));
    }

    public function errors(): MessageBag
    {
        return $this->errors;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->errors->getMessages() as $field => $fieldMessages) {
            $messages[(string) $field] = array_values(array_filter((array) $fieldMessages, 'is_string'));
        }

        return $messages;
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

    /**
     * @return array<string, array<int, string>>
     */
    public function toArray(): array
    {
        return $this->messages();
    }
}
