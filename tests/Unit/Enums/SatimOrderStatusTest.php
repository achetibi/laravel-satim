<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimOrderStatus;

it('maps the SATIM order status codes', function () {
    expect(SatimOrderStatus::tryFrom(0))->toBe(SatimOrderStatus::REGISTERED_NOT_PAID)
        ->and(SatimOrderStatus::tryFrom(-1))->toBe(SatimOrderStatus::DECLINED)
        ->and(SatimOrderStatus::tryFrom(1))->toBe(SatimOrderStatus::APPROVED)
        ->and(SatimOrderStatus::tryFrom(2))->toBe(SatimOrderStatus::DEPOSITED)
        ->and(SatimOrderStatus::tryFrom(3))->toBe(SatimOrderStatus::REVERSED)
        ->and(SatimOrderStatus::tryFrom(4))->toBe(SatimOrderStatus::REFUNDED)
        ->and(SatimOrderStatus::tryFrom(6))->toBe(SatimOrderStatus::AUTHORIZATION_DECLINED)
        ->and(SatimOrderStatus::tryFrom(99))->toBeNull();
});
