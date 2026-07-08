<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimAbstractResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

beforeEach(fn () => app()->setLocale('en'));

/**
 * @param  array<string, mixed>  $data
 */
function responseWith(array $data): SatimAbstractResponse
{
    return new readonly class ($data) extends SatimAbstractResponse {
        public function successful(): bool
        {
            return true;
        }

        public function exposeDateTime(string ...$keys): ?DateTimeImmutable
        {
            return $this->dateTime(...$keys);
        }

        /**
         * @return array<string, string>
         */
        public function exposePairs(string ...$keys): array
        {
            return $this->pairs(...$keys);
        }

        /**
         * @return array<array-key, mixed>
         */
        public function exposeNested(string ...$keys): array
        {
            return $this->nested(...$keys);
        }
    };
}

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

it('builds an immutable UTC datetime from a millisecond timestamp', function (): void {
    $dateTime = responseWith(['createdAt' => 1783419253424])->exposeDateTime('createdAt');

    expect($dateTime)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($dateTime->getTimestamp())->toBe(1783419253)
        ->and($dateTime->format('Y-m-d\TH:i:s.vP'))->toBe('2026-07-07T10:14:13.424+00:00');
});

it('accepts a numeric-string timestamp and falls back across keys', function (): void {
    $dateTime = responseWith(['ts' => '1783419253424'])->exposeDateTime('missing', 'ts');

    expect($dateTime?->getTimestamp())->toBe(1783419253);
});

it('returns null when the timestamp is absent or not numeric', function (): void {
    expect(responseWith([])->exposeDateTime('createdAt'))->toBeNull()
        ->and(responseWith(['createdAt' => 'nope'])->exposeDateTime('createdAt'))->toBeNull();
});

it('flattens a name/value list into an associative array', function (): void {
    $response = responseWith([
        'merchantOrderParams' => [
            ['name' => 'disablePhone', 'value' => 'true'],
            ['name' => 'udf1', 'value' => 'RES202600008'],
        ],
    ]);

    expect($response->exposePairs('merchantOrderParams'))->toBe([
        'disablePhone' => 'true',
        'udf1' => 'RES202600008',
    ]);
});

it('returns an empty array when the name/value list is absent or malformed', function (): void {
    expect(responseWith([])->exposePairs('merchantOrderParams'))->toBe([])
        ->and(responseWith(['merchantOrderParams' => 'nope'])->exposePairs('merchantOrderParams'))->toBe([]);
});

it('returns a nested associative sub-array as-is', function (): void {
    $response = responseWith(['cardAuthInfo' => ['pan' => '628058**7215', 'expiration' => '202701']]);

    expect($response->exposeNested('cardAuthInfo'))->toBe(['pan' => '628058**7215', 'expiration' => '202701'])
        ->and(responseWith([])->exposeNested('cardAuthInfo'))->toBe([]);
});
