<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use LaravelSatim\Exceptions\SatimApiServerException;
use LaravelSatim\Http\SatimHttpClient;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('can calls the API and returns a JSON response', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => Http::response(['ErrorCode' => '0']),
    ]);

    $client = new SatimHttpClient();

    $response = $client->call('register.do', ['key' => 'value']);

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('sends params in the body using POST by default', function () {
    Http::fake(function ($request) {
        expect($request->method())->toBe('POST');
        expect($request->url())->not->toContain('key=value');
        expect($request->data())->toBe(['key' => 'value']);

        return Http::response(['ErrorCode' => '0']);
    });

    $client = new SatimHttpClient();

    $response = $client->call('register.do', ['key' => 'value']);

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can send params in the query string when method is GET', function () {
    config()?->set('satim.http_client.method', 'GET');

    Http::fake(function ($request) {
        expect($request->method())->toBe('GET');
        expect($request->url())->toContain('key=value');

        return Http::response(['ErrorCode' => '0']);
    });

    $client = new SatimHttpClient();

    $response = $client->call('register.do', ['key' => 'value']);

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can formats the URL correctly regardless of slashes', function () {
    config()?->set('satim.api_url', 'https://test.satim.dz/payment/rest/////');

    Http::fake([
        'https://test.satim.dz/payment/rest/register.do' => Http::response(['ErrorCode' => '0']),
    ]);

    $client = new SatimHttpClient();

    $response = $client->call('/register.do');

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can returns null if response has empty JSON body', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $client = new SatimHttpClient();

    $response = $client->call('register.do');

    expect($response)->toBeNull();
});

it('throws exception on server error', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => Http::response('Internal Server Error', 500, ['Content-Type' => 'application/json']),
    ]);

    $client = new SatimHttpClient();

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'Server Error: Internal Server Error (500).');

it('throws exception if API URL is not configured', function () {
    config()?->set('satim.api_url', null);

    $client = new SatimHttpClient();

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'SATIM API URL is not configured.');

it('throws exception on connection failure', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => fn () => throw new ConnectionException('Connection failed'),
    ]);

    $client = new SatimHttpClient();

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'Connection failed');

it('throws if satim.api_url config is empty', function () {
    config()?->set('satim.api_url', '');

    $client = new SatimHttpClient();
    $client->call('any');
})->throws(SatimApiServerException::class, 'SATIM API URL is not configured.');

it('preserves the original throwable as the previous exception', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => fn () => throw new ConnectionException('Connection failed'),
    ]);

    $client = new SatimHttpClient();

    try {
        $client->call('register.do');
        $this->fail('Expected a SatimApiServerException to be thrown.');
    } catch (SatimApiServerException $e) {
        expect($e->getPrevious())->toBeInstanceOf(ConnectionException::class)
            ->and($e->getMessage())->toBe('Connection failed');
    }
});

it('does not double-wrap its own server error exception', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => Http::response('Internal Server Error', 500, ['Content-Type' => 'application/json']),
    ]);

    $client = new SatimHttpClient();

    try {
        $client->call('register.do');
        $this->fail('Expected a SatimApiServerException to be thrown.');
    } catch (SatimApiServerException $e) {
        expect($e->getPrevious())->toBeNull()
            ->and($e->getMessage())->toBe('Server Error: Internal Server Error (500).');
    }
});
