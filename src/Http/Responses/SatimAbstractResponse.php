<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Exceptions\SatimResponseException;
use Psr\Http\Message\ResponseInterface;

abstract readonly class SatimAbstractResponse implements SatimResponseInterface
{
    public function __construct(
        protected array $data
    ) {
    }

    public function raw(): array
    {
        return $this->data;
    }

    /**
     * @throws SatimResponseException
     */
    public static function fromPsr(ResponseInterface $response): static
    {
        $decoded = json_decode((string) $response->getBody(), true);
        $status = $response->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw SatimResponseException::httpError(
                $status,
                $response->getReasonPhrase()
            );
        }

        if (! is_array($decoded) || $decoded === []) {
            throw SatimResponseException::malformed();
        }

        return (new static($decoded))->throwIfFailed();
    }

    /**
     * @throws SatimResponseException
     */
    protected function throwIfFailed(): static
    {
        if (! $this->successful()) {
            $errorCode = (string) ($this->data['errorCode'] ?? $this->data['ErrorCode'] ?? 'unknown');
            $errorMessage = $this->data['errorMessage'] ?? $this->data['ErrorMessage'] ?? null;

            if ($this instanceof SatimConfirmResponse) {
                $errorCode = $this->respCode() ?? $errorCode;
                $errorMessage = $this->failureMessage() ?? $errorMessage;
            }

            throw SatimResponseException::fromCode($errorCode, $errorMessage);
        }

        return $this;
    }
}
