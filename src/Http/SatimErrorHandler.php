<?php

declare(strict_types=1);

namespace LaravelSatim\Http;

use LaravelSatim\Exceptions\SatimAuthenticationException;
use LaravelSatim\Exceptions\SatimPaymentException;
use LaravelSatim\Exceptions\SatimResponseException;

class SatimErrorHandler
{
    /**
     * @param  array<array-key, mixed>  $response
     *
     * @throws SatimResponseException
     */
    public function forRegister(array $response): void
    {
        $this->guard($response, 'errorCode', [0], [
            1 => SatimPaymentException::class,
            3 => SatimPaymentException::class,
            4 => SatimPaymentException::class,
            5 => SatimAuthenticationException::class,
            14 => SatimPaymentException::class,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $response
     *
     * @throws SatimResponseException
     */
    public function forConfirm(array $response): void
    {
        // ErrorCode 3 (declined card / order state) is a business outcome carried
        // by the confirmation response, not an API error: it must not throw.
        $this->guard($response, 'ErrorCode', [0, 3], [
            2 => SatimPaymentException::class,
            5 => SatimAuthenticationException::class,
            6 => SatimPaymentException::class,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $response
     *
     * @throws SatimResponseException
     */
    public function forRefund(array $response): void
    {
        $this->guard($response, 'errorCode', [0], [
            5 => SatimAuthenticationException::class,
            6 => SatimPaymentException::class,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $response
     * @param  array<int, int>  $successCodes
     * @param  array<int, class-string<SatimResponseException>>  $map
     *
     * @throws SatimResponseException
     */
    private function guard(array $response, string $codeKey, array $successCodes, array $map): void
    {
        $rawCode = $response[$codeKey] ?? 0;
        $code = is_numeric($rawCode) ? (int) $rawCode : 0;

        if (in_array($code, $successCodes, true)) {
            return;
        }

        $rawMessage = $response['errorMessage'] ?? $response['ErrorMessage'] ?? null;
        $message = is_scalar($rawMessage) ? (string) $rawMessage : 'The SATIM request failed.';

        $class = $map[$code] ?? SatimResponseException::class;

        throw new $class($message, $code, $response);
    }
}
