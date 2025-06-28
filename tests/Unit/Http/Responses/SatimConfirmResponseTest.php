<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\AbstractSatimResponse;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim response classes', function () {
    expect(SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '402130',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre paiement a été accepté',
                'udf1' => '2018105301346',
                'respCode' => '00',
            ],
            'actionCode' => 0,
            'actionCodeDescription' => 'Votre paiement a été accepté',
            'ErrorCode' => '0',
            'ErrorMessage' => 'Success',
            'OrderStatus' => 2,
            'OrderNumber' => '1538298192',
            'Pan' => '628058**1011',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '00',
        ])
    ))
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->toBeInstanceOf(AbstractSatimResponse::class)
        ->toBeInstanceOf(SatimResponseInterface::class);
});

it('should confirm a valid credit card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '402130',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre paiement a été accepté',
                'udf1' => '2018105301346',
                'respCode' => '00',
            ],
            'actionCode' => 0,
            'actionCodeDescription' => 'Votre paiement a été accepté',
            'ErrorCode' => '0',
            'ErrorMessage' => 'Success',
            'OrderStatus' => 2,
            'OrderNumber' => '1538298192',
            'Pan' => '628058**1011',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '00',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->paymentAccepted())->toBeTrue()
        ->and($response->paymentRejected())->toBeFalse()
        ->and($response->cardValid())->toBeTrue()
        ->and($response->cardStolen())->toBeFalse()
        ->and($response->cardExpired())->toBeFalse()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(1000)
        ->and($response->orderNumber)->toBeString('1538298192')
        ->and($response->orderStatus)->toBeString('2')
        ->and($response->actionCode)->toBeString('0')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('00')
        ->and($response->successMessage())->not()->toBeNull();
});

it('should confirm a temporarily blocked card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque.Code erreur :37',
                'udf1' => '2018105301346',
                'respCode' => '37',
            ],
            'actionCode' => 203,
            'actionCodeDescription' => 'processing.error.203',
            'ErrorCode' => '3',
            'ErrorMessage' => 'Order is not confirmed due to order’s state',
            'OrderStatus' => 6,
            'OrderNumber' => '1538298193',
            'Pan' => '628058**6712',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '37',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardTemporarilyBlocked())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298193')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('203')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('37')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a lost card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur =>41',
                'udf1' => '2018105301346',
                'respCode' => '41',
            ],
            'actionCode' => 208,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur =>208',
            'ErrorCode' => '3',
            'ErrorMessage' => 'Order is not confirmed due to order’s state',
            'OrderStatus' => 6,
            'OrderNumber' => '1538298194',
            'Pan' => '628058**6316',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '41',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardLost())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298194')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('208')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('41')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a stolen card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur : 43',
                'udf1' => '2018105301346',
                'respCode' => '43',
            ],
            'actionCode' => 209,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur :209',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298195',
            'Pan' => '628058**6415',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '43',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardStolen())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298195')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('209')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('43')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card with invalid expiry date', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202708',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => "Votre transaction a été rejetée, Veuillez rectifier la date d'expiration sélectionnée. Code erreur: AD",
                'udf1' => '2018105301346',
                'respCode' => 'AD',
            ],
            'actionCode' => -1,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur: 1',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298196',
            'Pan' => '628058**6613',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => 'AD',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardInvalidExpiryDate())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298196')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('-1')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('AD')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm an unavailable card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202501',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur :62',
                'udf1' => '2018105301346',
                'respCode' => '62',
            ],
            'actionCode' => 125,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur: 1',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298197',
            'Pan' => '628058**3927',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '62',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardUnavailable())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298197')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('125')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('62')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card with exceeded limit', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez entrer un autre montant ou contacter votre banque. Code erreur : 61',
                'udf1' => '2018105301346',
                'respCode' => '61',
            ],
            'actionCode' => 121,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez entrer un autre montant ou contacter votre banque. Code erreur : 121',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298199',
            'Pan' => '628058**1110',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '61',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardLimitExceeded())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298198')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('121')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('61')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card with insufficient balance', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, Crédit insuffisant, veuillez recharger votre compte bancaire pour effectuer cette opération. Code erreur : 51',
                'udf1' => '2018105301346',
                'respCode' => '51',
            ],
            'actionCode' => 116,
            'actionCodeDescription' => 'Votre transaction a été rejetée, Crédit insuffisant, veuillez recharger votre compte bancaire pour effectuer cette opération. Code erreur : ',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298199',
            'Pan' => '628058**1219',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '51',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardBalanceInsufficient())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298199')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('121')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('51')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card with incorrect CVV2', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, Veuillez rectifier le CVV2 saisi. Code erreur: AB',
                'udf1' => '2018105301346',
                'respCode' => 'AB',
            ],
            'actionCode' => 111,
            'actionCodeDescription' => 'Votre transaction a été rejetée, Veuillez rectifier votre saisi oubien contacte votre banque. Code erreur: 111',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298200',
            'Pan' => '628058**6514',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => 'AB',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardInvalidCVV2())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298200')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('111')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('AB')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card with exceeded password attempts', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [],
            'actionCode' => 2003,
            'actionCodeDescription' => 'votre transaction a été rejete, vous avez déjà saisie 3 fois mot passe errone pour cela,Votre service e-paiement est bloqué , veuillez contacter votre banque, Code erreur :2003',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298201',
            'Pan' => '628058**1318',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardExceededPasswordAttempts())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298201')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('2003')
        ->and($response->params)->toBeArray()
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card not authorized for online payment service', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [],
            'actionCode' => 2003,
            'actionCodeDescription' => 'votre transaction a été rejete, vous avez déjà saisie 3 fois mot passe errone pour cela,Votre service e-paiement est bloqué , veuillez contacter votre banque, Code erreur :2003',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298202',
            'Pan' => '628058**7017',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardNotAuthorizedForOnlinePayment())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298202')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('2003')
        ->and($response->params)->toBeArray()
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm a card inactive for online payment service', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur: AE',
                'udf1' => '2018105301346',
                'respCode' => 'AE',
            ],
            'actionCode' => -1,
            'actionCodeDescription' => 'Votre transaction a été rejetée, veuillez contacter votre banque. Code erreur: 1',
            'ErrorCode' => '3',
            'ErrorMessage' => "Order is not confirmed due to order's state",
            'OrderStatus' => 6,
            'OrderNumber' => '1538298203',
            'Pan' => '628058**7116',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => 'AE',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardInactiveForOnlinePayment())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298203')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('-1')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('AE')
        ->and($response->errorMessage())->not()->toBeNull();
});

it('should confirm another valid credit card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '404270',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre paiement a été accepté',
                'udf1' => '2018105301346',
                'respCode' => '00',
            ],
            'actionCode' => 0,
            'actionCodeDescription' => 'Votre paiement a été accepté',
            'ErrorCode' => '0',
            'ErrorMessage' => 'Success',
            'OrderStatus' => 2,
            'OrderNumber' => '1538298204',
            'Pan' => '628058**7215',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '00',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->paymentAccepted())->toBeTrue()
        ->and($response->paymentRejected())->toBeFalse()
        ->and($response->cardValid())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(1000)
        ->and($response->orderNumber)->toBeString('1538298204')
        ->and($response->orderStatus)->toBeString('2')
        ->and($response->actionCode)->toBeString('0')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('00')
        ->and($response->successMessage())->not()->toBeNull();
});

it('should confirm a card with terminal amount ceiling exceeded', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '404410',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, Votre plafond a été dépassé, veuillez contacter votre banque. Code erreur : 3',
                'udf1' => '2018105301346',
                'respCode' => null,
            ],
            'actionCode' => '-2006',
            'actionCodeDescription' => 'Votre transaction a été rejetée, Votre plafond a été dépassé, veuillez contacter votre banque. Code erreur : 3',
            'ErrorCode' => '3',
            'ErrorMessage' => 'Success',
            'OrderStatus' => '6',
            'OrderNumber' => '1538298205',
            'Pan' => '628058**7314',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => null,
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->paymentConfirmed())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardExceededTransactionCeiling())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(1000)
        ->and($response->orderNumber)->toBeString('1538298205')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('-2006')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBeNull()
        ->and($response->successMessage())->not()->toBeNull();
});

it('should confirm an expired card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202212',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, Votre carte a expiré, veuillez contacter votre banque. Code erreur : 3',
                'udf1' => '2018105301346',
                'respCode' => null,
            ],
            'actionCode' => '-2006',
            'actionCodeDescription' => 'Votre transaction a été rejetée, Votre carte a expiré, veuillez contacter votre banque. Code erreur : 3',
            'ErrorCode' => '3',
            'ErrorMessage' => 'Order is not confirmed due to order’s state',
            'OrderStatus' => 6,
            'OrderNumber' => '1538298206',
            'Pan' => '628058**6615',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => null,
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardValid())->toBeFalse()
        ->and($response->cardExpired())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298206')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('-2006')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBeNull()
        ->and($response->errorMessage())->not()->toBeNull();
});
