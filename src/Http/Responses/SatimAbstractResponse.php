<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Exceptions\SatimResponseException;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class SatimAbstractResponse implements SatimResponseInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected array $data
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->data;
    }

    /**
     * @throws SatimResponseException
     */
    public static function fromPsr(ResponseInterface $response): static
    {
        $status = $response->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw SatimResponseException::httpError($status, $response->getReasonPhrase());
        }

        $decoded = json_decode((string) $response->getBody(), true);

        if (! is_array($decoded) || $decoded === []) {
            throw SatimResponseException::malformed();
        }

        /** @var array<string, mixed> $decoded */
        return (new static($decoded))->throwIfFailed();
    }

    /**
     * Return the first non-null value among the provided keys (dot notation supported).
     */
    protected function value(string ...$keys): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($this->data, $key);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    protected function string(string ...$keys): ?string
    {
        $value = $this->value(...$keys);

        return is_string($value) ? $value : null;
    }

    protected function integer(string ...$keys): ?int
    {
        $value = $this->value(...$keys);

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Read a monetary field expressed in centimes and return it in major units.
     */
    protected function money(string ...$keys): ?float
    {
        $value = $this->value(...$keys);

        return is_numeric($value) ? (float) $value / 100 : null;
    }

    /**
     * @throws SatimResponseException
     */
    protected function throwIfFailed(): static
    {
        if ($this->successful()) {
            return $this;
        }

        $rawCode = $this->value('errorCode', 'ErrorCode');
        $errorCode = is_scalar($rawCode) ? (string) $rawCode : 'unknown';
        $errorMessage = $this->string('errorMessage', 'ErrorMessage');

        if ($this instanceof SatimConfirmResponse) {
            $errorCode = $this->respCode() ?? $errorCode;
            $errorMessage = $this->message() ?? $errorMessage;
        }

        throw SatimResponseException::fromCode($errorCode, $errorMessage);
    }
}
