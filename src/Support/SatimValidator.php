<?php

declare(strict_types=1);

namespace LaravelSatim\Support;

use LaravelSatim\Exceptions\SatimValidationException;

final class SatimValidator
{
    /**
     * @var list<string>
     */
    private array $errors = [];

    public static function make(): self
    {
        return new self();
    }

    public function required(?string $value, string $label): self
    {
        if ($value === null || $value === '') {
            $this->errors[] = "The {$label} is required.";
        }

        return $this;
    }

    /**
     * AN..$max — alphanumeric, up to $max characters.
     */
    public function alphanumeric(?string $value, string $label, int $max): self
    {
        if ($value !== null && $value !== '' && preg_match('/^[A-Za-z0-9]{1,'.$max.'}$/', $value) !== 1) {
            $this->errors[] = "The {$label} must be alphanumeric and at most {$max} characters.";
        }

        return $this;
    }

    /**
     * ANS..$max — alphanumeric and special characters, no spaces, up to $max.
     */
    public function token(?string $value, string $label, int $max = 20): self
    {
        if ($value !== null && $value !== '' && preg_match('/^\S{1,'.$max.'}$/', $value) !== 1) {
            $this->errors[] = "The {$label} must be at most {$max} characters and contain no spaces.";
        }

        return $this;
    }

    /**
     * AN..$max — a valid URL no longer than $max characters.
     */
    public function url(?string $value, string $label, int $max = 512): self
    {
        if ($value !== null && $value !== '' && (mb_strlen($value) > $max || filter_var($value, FILTER_VALIDATE_URL) === false)) {
            $this->errors[] = "The {$label} must be a valid URL of at most {$max} characters.";
        }

        return $this;
    }

    public function maxLength(?string $value, string $label, int $max): self
    {
        if ($value !== null && mb_strlen($value) > $max) {
            $this->errors[] = "The {$label} must not be greater than {$max} characters.";
        }

        return $this;
    }

    /**
     * N 1..12 — amount in DZD: at least $min, at most two decimals, fitting in
     * twelve digits of centimes once multiplied by 100.
     */
    public function amount(float $value, float $min = 50, float $max = 9_999_999_999.99): self
    {
        if ($value < $min) {
            $this->errors[] = "The amount must be at least {$min}.";
        } elseif ($value > $max) {
            $this->errors[] = 'The amount is too large.';
        } elseif (round($value, 2) !== $value) {
            $this->errors[] = 'The amount must not have more than two decimal places.';
        }

        return $this;
    }

    /**
     * @return list<string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @throws SatimValidationException
     */
    public function validate(): void
    {
        if ($this->errors !== []) {
            throw new SatimValidationException($this->errors[0], $this->errors);
        }
    }
}
