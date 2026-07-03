<?php

declare(strict_types=1);

use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimOrderStatus;
use LaravelSatim\Http\Responses\SatimConfirmResponse;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function confirmPayload(array $overrides = []): array
{
    return array_replace([
        'expiration' => '202701',
        'cardholderName' => 'cardholder Name',
        'depositAmount' => 100320,
        'currency' => '012',
        'authorizationResponseId' => '913180',
        'approvalCode' => '913180',
        'actionCode' => 0,
        'actionCodeDescription' => 'Votre paiement a été accepté',
        'ErrorCode' => '0',
        'ErrorMessage' => 'Success',
        'OrderStatus' => 2,
        'OrderNumber' => 'CMD0000004',
        'Pan' => '6280****7215',
        'Amount' => 100320,
        'Ip' => '10.12.12.14',
        'clientId' => 'client-1',
        'bindingId' => 'binding-1',
        'paymentAccountReference' => 'par-1',
        'Description' => 'desc',
        'params' => ['respCode_desc' => 'Votre paiement a été accepté', 'udf1' => 'Bill00001', 'respCode' => '00'],
        'SvfeResponse' => '00',
    ], $overrides);
}

it('parses every documented SATIM field', function () {
    $response = SatimConfirmResponse::fromResponse(confirmPayload());

    expect($response->expiration)->toBe('202701')
        ->and($response->cardholderName)->toBe('cardholder Name')
        ->and($response->depositAmount)->toBe(1003.20)
        ->and($response->currency)->toBe(SatimCurrency::DZD)
        ->and($response->pan)->toBe('6280****7215')
        ->and($response->approvalCode)->toBe('913180')
        ->and($response->authorizationResponseId)->toBe('913180')
        ->and($response->orderNumber)->toBe('CMD0000004')
        ->and($response->amount)->toBe(1003.20)
        ->and($response->svfeResponse)->toBe('00')
        ->and($response->ip)->toBe('10.12.12.14')
        ->and($response->clientId)->toBe('client-1')
        ->and($response->bindingId)->toBe('binding-1')
        ->and($response->paymentAccountReference)->toBe('par-1')
        ->and($response->description)->toBe('desc')
        ->and($response->orderStatus)->toBe('2')
        ->and($response->actionCode)->toBe('0')
        ->and($response->actionCodeDescription)->toBe('Votre paiement a été accepté')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBe('Success')
        ->and($response->params)->toBe(['udf1' => 'Bill00001', 'respCode' => '00', 'respCode_desc' => 'Votre paiement a été accepté']);
});

it('resolves the order status enum and lifecycle helpers', function (int $status, string $method) {
    $response = SatimConfirmResponse::fromResponse(confirmPayload(['OrderStatus' => $status]));

    expect($response->status())->toBe(SatimOrderStatus::from($status))
        ->and($response->{$method}())->toBeTrue();
})->with([
    'deposited' => [2, 'paid'],
    'approved' => [1, 'approved'],
    'reversed' => [3, 'reversed'],
    'refunded' => [4, 'refunded'],
    'registered not paid' => [0, 'registeredNotPaid'],
    'authorization declined' => [6, 'declined'],
    'generic declined' => [-1, 'declined'],
]);

it('treats a deposited order as successful and accepted', function () {
    $response = SatimConfirmResponse::fromResponse(confirmPayload());

    expect($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->cardValid())->toBeTrue()
        ->and($response->paymentAccepted())->toBeTrue()
        ->and($response->successMessage())->toBe('Votre paiement a été accepté');
});

it('interprets a declined card without throwing', function () {
    $response = SatimConfirmResponse::fromResponse(confirmPayload([
        'depositAmount' => 0,
        'actionCode' => 116,
        'ErrorCode' => '3',
        'OrderStatus' => 6,
        'params' => ['respCode_desc' => 'Crédit insuffisant', 'udf1' => 'Bill00001', 'respCode' => '51'],
    ]));

    expect($response->cardBalanceInsufficient())->toBeTrue()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->declined())->toBeTrue()
        ->and($response->errorMessage())->toBe('Crédit insuffisant');
});
