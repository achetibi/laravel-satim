<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use DateTimeImmutable;
use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Support\SatimCaster;
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
        return SatimCaster::string($this->value(...$keys));
    }

    protected function integer(string ...$keys): ?int
    {
        return SatimCaster::integer($this->value(...$keys));
    }

    protected function money(string ...$keys): ?float
    {
        $value = SatimCaster::float($this->value(...$keys));

        return $value !== null ? $value / 100 : null;
    }

    protected function dateTime(string ...$keys): ?DateTimeImmutable
    {
        return SatimCaster::dateTime($this->value(...$keys));
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function nested(string ...$keys): array
    {
        $value = $this->value(...$keys);

        return is_array($value) ? $value : [];
    }

    /**
     * @return array<string, string>
     */
    protected function pairs(string ...$keys): array
    {
        $items = $this->value(...$keys);

        if (! is_array($items)) {
            return [];
        }

        $pairs = [];

        foreach ($items as $item) {
            if (is_array($item) && isset($item['name'], $item['value']) && is_scalar($item['name']) && is_scalar($item['value'])) {
                $pairs[(string) $item['name']] = (string) $item['value'];
            }
        }

        return $pairs;
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
            $errorCode = $this->params()->respCode ?? $errorCode;
            $errorMessage = $this->message() ?? $errorMessage;
        }

        throw SatimResponseException::fromCode($errorCode, $errorMessage);
    }
}
