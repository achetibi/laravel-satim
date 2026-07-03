<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimAuthenticationException;
use LaravelSatim\Exceptions\SatimPaymentException;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\SatimErrorHandler;

function handler(): SatimErrorHandler
{
    return new SatimErrorHandler();
}

it('does not throw on successful codes', function () {
    handler()->forRegister(['errorCode' => '0']);
    handler()->forConfirm(['ErrorCode' => '0']);
    handler()->forRefund(['errorCode' => '0']);

    expect(true)->toBeTrue();
});

it('does not throw on a declined card (confirm ErrorCode 3)', function () {
    handler()->forConfirm(['ErrorCode' => '3', 'OrderStatus' => 6]);

    expect(true)->toBeTrue();
});

it('maps register error codes to typed exceptions', function () {
    expect(fn () => handler()->forRegister(['errorCode' => 5]))->toThrow(SatimAuthenticationException::class)
        ->and(fn () => handler()->forRegister(['errorCode' => 1]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forRegister(['errorCode' => 3]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forRegister(['errorCode' => 4]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forRegister(['errorCode' => 14]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forRegister(['errorCode' => 7]))->toThrow(SatimResponseException::class);
});

it('maps confirm error codes to typed exceptions', function () {
    expect(fn () => handler()->forConfirm(['ErrorCode' => 5]))->toThrow(SatimAuthenticationException::class)
        ->and(fn () => handler()->forConfirm(['ErrorCode' => 2]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forConfirm(['ErrorCode' => 6]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forConfirm(['ErrorCode' => 7]))->toThrow(SatimResponseException::class);
});

it('maps refund error codes to typed exceptions', function () {
    expect(fn () => handler()->forRefund(['errorCode' => 5]))->toThrow(SatimAuthenticationException::class)
        ->and(fn () => handler()->forRefund(['errorCode' => 6]))->toThrow(SatimPaymentException::class)
        ->and(fn () => handler()->forRefund(['errorCode' => 7]))->toThrow(SatimResponseException::class);
});

it('carries the SATIM error code, message and raw response on the exception', function () {
    try {
        handler()->forRegister(['errorCode' => 5, 'errorMessage' => 'Access is denied.']);
        $this->fail('Expected a SatimAuthenticationException.');
    } catch (SatimAuthenticationException $e) {
        expect($e->getMessage())->toBe('Access is denied.')
            ->and($e->errorCode())->toBe(5)
            ->and($e->context())->toBe(['errorCode' => 5, 'errorMessage' => 'Access is denied.']);
    }
});
