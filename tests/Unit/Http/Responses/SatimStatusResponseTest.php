<?php

declare(strict_types=1);

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\OrderStatus;
use LaravelSatim\Http\Responses\SatimStatusResponse;

it('is successful only when deposited with error code 0', function (): void {
    expect((new SatimStatusResponse(['errorCode' => '0', 'orderStatus' => 2]))->successful())->toBeTrue()
        ->and((new SatimStatusResponse(['errorCode' => '0', 'orderStatus' => 1]))->successful())->toBeFalse()
        ->and((new SatimStatusResponse(['errorCode' => '5', 'orderStatus' => 2]))->successful())->toBeFalse();
});

it('resolves the order status enum and currency', function (): void {
    expect((new SatimStatusResponse(['orderStatus' => 2]))->orderStatus())->toBe(OrderStatus::DEPOSITED)
        ->and((new SatimStatusResponse(['currency' => '012']))->currency())->toBe(Currency::DZD);
});

it('reads millisecond timestamps as UTC datetimes', function (): void {
    $response = new SatimStatusResponse(['date' => 1783419253424, 'authDateTime' => 1783419253424]);

    expect($response->date()?->format('Y-m-d\TH:i:s.vP'))->toBe('2026-07-07T10:14:13.424+00:00')
        ->and($response->authDateTime()?->format('Y-m-d\TH:i:s.vP'))->toBe('2026-07-07T10:14:13.424+00:00');
});

it('exposes scalar fields', function (): void {
    $response = new SatimStatusResponse([
        'actionCode' => 0,
        'actionCodeDescription' => 'Approved',
        'amount' => 150000,
        'authRefNum' => 'AR1',
        'fraudLevel' => 0,
        'ip' => '10.0.0.1',
        'orderDescription' => 'Order',
        'orderNumber' => 'ORD123',
        'terminalId' => 'TERM1',
        'errorCode' => '0',
    ]);

    expect($response->actionCode())->toBe(0)
        ->and($response->message())->toBe('Approved')
        ->and($response->amount())->toBe(1500.0)
        ->and($response->authRefNum())->toBe('AR1')
        ->and($response->fraudLevel())->toBe(0)
        ->and($response->ip())->toBe('10.0.0.1')
        ->and($response->orderDescription())->toBe('Order')
        ->and($response->orderNumber())->toBe('ORD123')
        ->and($response->terminalId())->toBe('TERM1');
});

it('exposes merchant order params as a typed value object', function (): void {
    $response = new SatimStatusResponse([
        'merchantOrderParams' => [
            ['name' => 'disablePhone', 'value' => 'true'],
            ['name' => 'force_terminal_id', 'value' => 'XXXXXXXXXX'],
            ['name' => 'udf1', 'value' => 'RES202600008'],
            ['name' => 'transmissionDate', 'value' => '1783419253425'],
            ['name' => 'disableEmail', 'value' => 'true'],
        ],
        'attributes' => [
            ['name' => 'mdOrder', 'value' => 'ehf9z2yvvThwQ4AACW2G'],
        ],
    ]);

    $params = $response->merchantOrderParams();

    expect($params->disablePhone)->toBeTrue()
        ->and($params->disableEmail)->toBeTrue()
        ->and($params->forceTerminalId)->toBe('XXXXXXXXXX')
        ->and($params->udf1)->toBe('RES202600008')
        ->and($params->transmissionDate)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($params->transmissionDate?->format('Y-m-d\TH:i:s.vP'))->toBe('2026-07-07T10:14:13.425+00:00')
        ->and($response->attributes()->mdOrder)->toBe('ehf9z2yvvThwQ4AACW2G');
});

it('exposes the card auth info as a typed value object', function (): void {
    $response = new SatimStatusResponse([
        'cardAuthInfo' => [
            'expiration' => '202701',
            'authorizationResponseId' => '222340',
            'approvalCode' => '222340',
            'pan' => '628058**7215',
        ],
    ]);

    $card = $response->cardAuthInfo();

    expect($card->pan)->toBe('628058**7215')
        ->and($card->expiration)->toBe('202701')
        ->and($card->approvalCode)->toBe('222340')
        ->and($card->authorizationResponseId)->toBe('222340');
});
