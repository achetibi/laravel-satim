<?php

declare(strict_types=1);

use LaravelSatim\Support\SatimCaster;

it('casts strings', function (): void {
    expect(SatimCaster::string('hello'))->toBe('hello')
        ->and(SatimCaster::string(42))->toBeNull()
        ->and(SatimCaster::string(null))->toBeNull();
});

it('casts numeric values to int and float', function (): void {
    expect(SatimCaster::integer('42'))->toBe(42)
        ->and(SatimCaster::integer('x'))->toBeNull()
        ->and(SatimCaster::float('12.5'))->toBe(12.5)
        ->and(SatimCaster::float('x'))->toBeNull();
});

it('parses SATIM string booleans', function (): void {
    expect(SatimCaster::boolean('true'))->toBeTrue()
        ->and(SatimCaster::boolean('false'))->toBeFalse()
        ->and(SatimCaster::boolean(true))->toBeTrue()
        ->and(SatimCaster::boolean(1))->toBeTrue()
        ->and(SatimCaster::boolean('nope'))->toBeNull()
        ->and(SatimCaster::boolean(null))->toBeNull();
});

it('casts millisecond timestamps to UTC datetimes', function (): void {
    $dateTime = SatimCaster::dateTime('1783419253424');

    expect($dateTime)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($dateTime?->format('Y-m-d\TH:i:s.vP'))->toBe('2026-07-07T10:14:13.424+00:00')
        ->and(SatimCaster::dateTime('nope'))->toBeNull()
        ->and(SatimCaster::dateTime(null))->toBeNull();
});
