<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimFundingType;

it('exposes the documented bill payment codes', function () {
    expect(SatimFundingType::BILL_PAYMENT->value)->toBe('CP')
        ->and(SatimFundingType::BILL_PAYMENT_698->value)->toBe('698');
});

it('resolves both bill payment codes from their value', function () {
    expect(SatimFundingType::from('CP'))->toBe(SatimFundingType::BILL_PAYMENT)
        ->and(SatimFundingType::from('698'))->toBe(SatimFundingType::BILL_PAYMENT_698)
        ->and(SatimFundingType::tryFrom('XX'))->toBeNull();
});
