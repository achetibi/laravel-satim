<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Http\Responses\SatimResponseData;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('casts values to their expected type', function () {
    $data = SatimResponseData::from([
        'OrderStatus' => 2,
        'Amount' => '100000',
        'depositAmount' => 100320,
    ]);

    expect($data->string('OrderStatus'))->toBe('2')
        ->and($data->integer('Amount'))->toBe(100000)
        ->and($data->float('depositAmount'))->toBe(100320.0);
});

it('reads nested values using dot notation', function () {
    $data = SatimResponseData::from([
        'params' => [
            'respCode' => '00',
            'udf1' => 'Bill00001',
        ],
    ]);

    expect($data->string('params.respCode'))->toBe('00')
        ->and($data->string('params.udf1'))->toBe('Bill00001');
});

it('returns defaults for missing keys', function () {
    $data = SatimResponseData::from(null);

    expect($data->string('missing'))->toBeNull()
        ->and($data->string('missing', 'fallback'))->toBe('fallback')
        ->and($data->integer('missing'))->toBeNull()
        ->and($data->float('missing'))->toBeNull()
        ->and($data->array('missing'))->toBe([])
        ->and($data->has('missing'))->toBeFalse();
});

it('resolves backed enums and falls back on unknown values', function () {
    $data = SatimResponseData::from(['currency' => '012', 'other' => '999']);

    expect($data->enum('currency', SatimCurrency::class))->toBe(SatimCurrency::DZD)
        ->and($data->enum('other', SatimCurrency::class))->toBeNull()
        ->and($data->enum('other', SatimCurrency::class, SatimCurrency::DZD))->toBe(SatimCurrency::DZD);
});
