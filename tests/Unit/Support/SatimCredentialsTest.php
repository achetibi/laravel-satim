<?php

declare(strict_types=1);

use LaravelSatim\Exceptions\SatimConfigurationException;
use LaravelSatim\Support\SatimCredentials;

it('builds credentials from config values', function () {
    $credentials = SatimCredentials::fromConfig('user', 'pass', 'term');

    expect($credentials->userName)->toBe('user')
        ->and($credentials->password)->toBe('pass')
        ->and($credentials->terminal)->toBe('term');
});

it('throws when a credential is empty', function () {
    SatimCredentials::fromConfig('user', '', 'term');
})->throws(SatimConfigurationException::class, 'SATIM credential [password] is not configured.');

it('throws when a credential is not a string', function () {
    SatimCredentials::fromConfig(null, 'pass', 'term');
})->throws(SatimConfigurationException::class, 'SATIM credential [username] is not configured.');
