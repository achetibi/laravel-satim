<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\HttpFactory;
use LaravelSatim\Client\SatimHttpClient;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A recording PSR-18 client that optionally throws.
 */
function fakeClient(ResponseInterface $response, ?Throwable $throw = null): ClientInterface
{
    return new class ($response, $throw) implements ClientInterface {
        public ?RequestInterface $lastRequest = null;

        public function __construct(
            private readonly ResponseInterface $response,
            private readonly ?Throwable $throw,
        ) {
        }

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            $this->lastRequest = $request;

            if ($this->throw !== null) {
                throw $this->throw;
            }

            return $this->response;
        }
    };
}

function registerRequest(): SatimRegisterRequest
{
    return new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'U1',
    );
}

it('sends a POST request with credentials and json-encoded jsonParams', function (): void {
    $client = fakeClient(jsonResponse(['errorCode' => '0']));
    $factory = new HttpFactory();
    $http = new SatimHttpClient($client, $factory, $factory, satimConfig());

    $http->send('/register.do', registerRequest());

    $sent = $client->lastRequest;
    expect($sent)->not->toBeNull()
        ->and($sent->getMethod())->toBe('POST')
        ->and((string) $sent->getUri())->toBe('https://test2.satim.dz/payment/rest/register.do')
        ->and($sent->getHeaderLine('Content-Type'))->toBe('application/x-www-form-urlencoded');

    parse_str((string) $sent->getBody(), $parsed);
    expect($parsed['userName'])->toBe('user')
        ->and($parsed['password'])->toBe('pass');

    $jsonParams = json_decode($parsed['jsonParams'], true);
    expect($jsonParams['udf1'])->toBe('U1')
        ->and($jsonParams['force_terminal_id'])->toBe('terminal');
});

it('omits the terminal id when it is not configured', function (): void {
    $client = fakeClient(jsonResponse(['errorCode' => '0']));
    $factory = new HttpFactory();
    $http = new SatimHttpClient($client, $factory, $factory, satimConfig(['credentials' => ['terminal_id' => null]]));

    $http->send('/register.do', registerRequest());

    parse_str((string) $client->lastRequest->getBody(), $parsed);
    $jsonParams = json_decode($parsed['jsonParams'], true);

    expect($jsonParams)->not->toHaveKey('force_terminal_id');
});

it('builds a GET request with the payload in the query string', function (): void {
    $client = fakeClient(jsonResponse(['errorCode' => '0']));
    $factory = new HttpFactory();
    $http = new SatimHttpClient($client, $factory, $factory, satimConfig(['http' => ['method' => 'GET']]));

    $http->send('/register.do', registerRequest());

    $sent = $client->lastRequest;
    expect($sent->getMethod())->toBe('GET')
        ->and((string) $sent->getUri())->toContain('userName=user')
        ->and((string) $sent->getBody())->toBe('');
});

it('wraps transport failures in a SatimConnectionException', function (): void {
    $throw = new class ('gateway is down') extends RuntimeException implements ClientExceptionInterface {};
    $client = fakeClient(jsonResponse([]), $throw);
    $factory = new HttpFactory();
    $http = new SatimHttpClient($client, $factory, $factory, satimConfig());

    expect(fn () => $http->send('/register.do', registerRequest()))
        ->toThrow(SatimConnectionException::class);
});
