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

    $client = new SatimHttpClient;

    $response = $client->call('register.do', ['key' => 'value']);

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can adds query params to the URL', function () {
    Http::fake(function ($request) {
        expect($request->url())->toContain('key=value');

        return Http::response(['ErrorCode' => '0']);
    });

    $client = new SatimHttpClient;

    $response = $client->call('register.do', ['key' => 'value']);

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can formats the URL correctly regardless of slashes', function () {
    config()?->set('satim.api_url', 'https://test.satim.dz/payment/rest/////');

    Http::fake([
        'https://test.satim.dz/payment/rest/register.do' => Http::response(['ErrorCode' => '0']),
    ]);

    $client = new SatimHttpClient;

    $response = $client->call('/register.do');

    expect($response)->toBe(['ErrorCode' => '0']);
});

it('can returns null if response has empty JSON body', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $client = new SatimHttpClient;

    $response = $client->call('register.do');

    expect($response)->toBeNull();
});

it('throws exception on server error', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => Http::response('Internal Server Error', 500, ['Content-Type' => 'application/json']),
    ]);

    $client = new SatimHttpClient;

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'Server error: Internal Server Error (500).');

it('throws exception if API URL is not configured', function () {
    config()?->set('satim.api_url', null);

    $client = new SatimHttpClient;

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'SATIM API URL is not configured.');

it('throws exception on connection failure', function () {
    Http::fake([
        'https://test.satim.dz/payment/rest/*' => fn () => throw new ConnectionException('Connection failed'),
    ]);

    $client = new SatimHttpClient;

    $client->call('register.do');
})->throws(SatimApiServerException::class, 'Connection failed');

it('throws if satim.api_url config is empty', function () {
    config()?->set('satim.api_url', '');

    $client = new SatimHttpClient;
    $client->call('any');
})->throws(SatimApiServerException::class, 'SATIM API URL is not configured.');
