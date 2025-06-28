<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(LaravelSatim\Tests\TestCase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function fakeApi(string $endpoint, array $data = [])
{
    $url = implode('/', [rtrim(config('satim.api_url')), ltrim($endpoint, '/')]);
    $client = Http::fake([$url => Http::response($data)]);

    return $client->get($url)->json();
}

function registerEndpoint(array $data = [])
{
    return fakeApi('/register.do', $data);
}

function confirmEndpoint(array $data = [])
{
    return fakeApi('/confirmOrder.do', $data);
}

function refundEndpoint(array $data = [])
{
    return fakeApi('/refund.do', $data);
}
