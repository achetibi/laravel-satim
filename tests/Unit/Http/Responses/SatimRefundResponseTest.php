<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\SatimRefundResponse;

it('parses the refund response message', function () {
    $response = SatimRefundResponse::fromResponse(['errorCode' => '0', 'errorMessage' => 'Success']);

    expect($response)->toBeInstanceOf(SatimResponseInterface::class)
        ->and($response->errorMessage)->toBe('Success');
});

it('handles an empty response gracefully', function () {
    expect(SatimRefundResponse::fromResponse(null)->errorMessage)->toBeNull();
});
