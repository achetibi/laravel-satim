<?php

declare(strict_types=1);

use LaravelSatim\Enums\OrderStatus;

it('exposes the SATIM order status codes', function (): void {
    expect(OrderStatus::REGISTERED_NOT_PAID->value)->toBe(0)
        ->and(OrderStatus::DECLINED->value)->toBe(-1)
        ->and(OrderStatus::APPROVED->value)->toBe(1)
        ->and(OrderStatus::DEPOSITED->value)->toBe(2)
        ->and(OrderStatus::REVERSED->value)->toBe(3)
        ->and(OrderStatus::REFUNDED->value)->toBe(4)
        ->and(OrderStatus::AUTHORIZATION_DECLINED->value)->toBe(6);
});

it('resolves an order status from its integer code', function (): void {
    expect(OrderStatus::tryFrom(2))->toBe(OrderStatus::DEPOSITED)
        ->and(OrderStatus::tryFrom(99))->toBeNull();
});
