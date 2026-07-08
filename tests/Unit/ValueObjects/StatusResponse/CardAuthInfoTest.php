<?php

declare(strict_types=1);

use LaravelSatim\ValueObjects\StatusResponse\CardAuthInfo;

it('maps the card auth info fields', function (): void {
    $card = CardAuthInfo::fromArray([
        'expiration' => '202812',
        'authorizationResponseId' => '778812',
        'approvalCode' => 'A99Z01',
        'cardholderName' => 'RAHIM C',
        'pan' => '400000**1234',
    ]);

    expect($card->pan)->toBe('400000**1234')
        ->and($card->expiration)->toBe('202812')
        ->and($card->approvalCode)->toBe('A99Z01')
        ->and($card->authorizationResponseId)->toBe('778812')
        ->and($card->cardholderName)->toBe('RAHIM C');
});

it('defaults every field to null when built from an empty array', function (): void {
    $card = CardAuthInfo::fromArray([]);

    expect($card->pan)->toBeNull()
        ->and($card->expiration)->toBeNull()
        ->and($card->approvalCode)->toBeNull()
        ->and($card->authorizationResponseId)->toBeNull()
        ->and($card->cardholderName)->toBeNull();
});
