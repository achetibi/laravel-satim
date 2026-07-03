<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use LaravelSatim\Exceptions\SatimConfigurationException;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\SatimHttpClient;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

function registerRequest(): SatimRegisterRequest
{
    return SatimRegisterRequest::make(
        orderNumber: 'ORDER123',
        amount: 100.50,
        returnUrl: 'https://merchant.test/return',
        udf1: 'udf1',
    );
}

beforeEach(fn () => config()->set('satim.http_client.retry', 0));

it('injects credentials and defaults, and posts to the endpoint by default', function () {
    Http::fake(function ($request) {
        expect($request->method())->toBe('POST');
        expect($request->url())->toBe('https://test2.satim.dz/payment/rest/register.do');

        $data = $request->data();
        expect($data['userName'])->toBe('test_username')
            ->and($data['password'])->toBe('test_password')
            ->and($data['orderNumber'])->toBe('ORDER123')
            ->and($data['amount'])->toBe(10050)
            ->and($data['currency'])->toBe('012')
            ->and($data['language'])->toBe('EN');

        $jsonParams = json_decode($data['jsonParams'], true);
        expect($jsonParams['force_terminal_id'])->toBe('test_terminal')
            ->and($jsonParams['udf1'])->toBe('udf1');

        return Http::response(['errorCode' => '0']);
    });

    expect(app(SatimHttpClient::class)->send('/register.do', registerRequest()))
        ->toBe(['errorCode' => '0']);
});

it('sends parameters in the query string when method is GET', function () {
    config()->set('satim.http_client.method', 'GET');

    Http::fake(function ($request) {
        expect($request->method())->toBe('GET');
        expect($request->url())->toContain('userName=test_username');

        return Http::response(['errorCode' => '0']);
    });

    expect(app(SatimHttpClient::class)->send('/register.do', registerRequest()))
        ->toBe(['errorCode' => '0']);
});

it('returns an empty array on an empty JSON body', function () {
    Http::fake(['*' => Http::response()]);

    expect(app(SatimHttpClient::class)->send('/register.do', registerRequest()))->toBe([]);
});

it('throws a connection exception on a server error', function () {
    Http::fake(['*' => Http::response('Internal Server Error', 500)]);

    app(SatimHttpClient::class)->send('/register.do', registerRequest());
})->throws(SatimConnectionException::class, 'Server Error: Internal Server Error (500).');

it('wraps a transport failure and preserves the previous exception', function () {
    Http::fake(['*' => fn () => throw new ConnectionException('Connection failed')]);

    try {
        app(SatimHttpClient::class)->send('/register.do', registerRequest());
        $this->fail('Expected a SatimConnectionException.');
    } catch (SatimConnectionException $e) {
        expect($e->getMessage())->toBe('Connection failed')
            ->and($e->getPrevious())->toBeInstanceOf(ConnectionException::class);
    }
});

it('throws a configuration exception when the API URL is missing', function () {
    config()->set('satim.api_url', null);

    app(SatimHttpClient::class);
})->throws(SatimConfigurationException::class, 'SATIM API URL is not configured.');

it('throws a configuration exception when credentials are missing', function () {
    config()->set('satim.username', null);

    app(SatimHttpClient::class);
})->throws(SatimConfigurationException::class, 'SATIM credential [username] is not configured.');
