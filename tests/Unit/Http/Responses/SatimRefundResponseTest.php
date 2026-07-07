<?php

declare(strict_types=1);

use LaravelSatim\Http\Responses\SatimRefundResponse;

it('is successful when the error code is zero', function (): void {
    expect((new SatimRefundResponse(['errorCode' => '0']))->successful())->toBeTrue()
        ->and((new SatimRefundResponse(['errorCode' => '5']))->successful())->toBeFalse();
});

it('returns a null error code when absent', function (): void {
    expect((new SatimRefundResponse([]))->errorCode())->toBeNull();
});

it('exposes the error message', function (): void {
    expect((new SatimRefundResponse(['errorMessage' => 'refund failed']))->errorMessage())
        ->toBe('refund failed');
});
