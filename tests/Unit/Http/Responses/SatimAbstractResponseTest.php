<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

beforeEach(fn () => app()->setLocale('en'));

it('returns the raw decoded payload', function (): void {
    $response = new SatimRegisterResponse(['errorCode' => '0', 'formUrl' => 'https://pay.test']);

    expect($response->raw())->toBe(['errorCode' => '0', 'formUrl' => 'https://pay.test']);
});

it('throws an http error for a non 2xx status', function (): void {
    SatimRegisterResponse::fromPsr(jsonResponse(['errorCode' => '0'], 503));
})->throws(SatimResponseException::class);

it('throws a malformed exception for a non-json body', function (): void {
    SatimRegisterResponse::fromPsr(rawResponse('<html>not json</html>'));
})->throws(SatimResponseException::class);

it('throws a malformed exception for an empty json body', function (): void {
    SatimRegisterResponse::fromPsr(jsonResponse([]));
})->throws(SatimResponseException::class);

it('throws a gateway exception mapped from the error code', function (): void {
    try {
        SatimRegisterResponse::fromPsr(jsonResponse(['errorCode' => '5', 'errorMessage' => 'denied']));
        $this->fail('Expected a SatimResponseException.');
    } catch (SatimResponseException $e) {
        expect($e->errorCode)->toBe('5')
            ->and($e->getMessage())->toBe('Access denied (invalid credentials).');
    }
});

it('returns the response instance when the call is successful', function (): void {
    $response = SatimRegisterResponse::fromPsr(jsonResponse([
        'errorCode' => '0',
        'formUrl' => 'https://pay.test/form',
    ]));

    expect($response->successful())->toBeTrue();
});
