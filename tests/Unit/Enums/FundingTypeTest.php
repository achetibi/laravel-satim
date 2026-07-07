<?php

declare(strict_types=1);

use LaravelSatim\Enums\FundingType;

it('exposes the expected funding type indicators', function (): void {
    expect(FundingType::BILL_PAYMENT->value)->toBe('CP')
        ->and(FundingType::BILL_PAYMENT_698->value)->toBe('698');
});
