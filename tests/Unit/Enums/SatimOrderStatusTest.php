<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimOrderStatus;

it('maps the SATIM order status codes', function () {
    expect(SatimOrderStatus::tryFrom(0))->toBe(SatimOrderStatus::RegisteredNotPaid)
        ->and(SatimOrderStatus::tryFrom(-1))->toBe(SatimOrderStatus::Declined)
        ->and(SatimOrderStatus::tryFrom(1))->toBe(SatimOrderStatus::Approved)
        ->and(SatimOrderStatus::tryFrom(2))->toBe(SatimOrderStatus::Deposited)
        ->and(SatimOrderStatus::tryFrom(3))->toBe(SatimOrderStatus::Reversed)
        ->and(SatimOrderStatus::tryFrom(4))->toBe(SatimOrderStatus::Refunded)
        ->and(SatimOrderStatus::tryFrom(6))->toBe(SatimOrderStatus::AuthorizationDeclined)
        ->and(SatimOrderStatus::tryFrom(99))->toBeNull();
});
