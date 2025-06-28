<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Support\SatimResponseAccessor;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimConfirmResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class SatimConfirmResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public function __construct(
        public ?string $expiration = null,
        public ?string $cardholderName = null,
        public ?float $depositAmount = null,
        public ?SatimCurrency $currency = null,
        public ?string $pan = null,
        public ?string $approvalCode = null,
        public ?int $authCode = null,
        public ?string $orderNumber = null,
        public ?float $amount = null,
        public ?string $svfeResponse = null,
        public ?string $orderStatus = null,
        public ?string $actionCode = null,
        public ?string $actionCodeDescription = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public ?string $ip = null,
        public array $params = []
    ) {
        parent::__construct(
            orderStatus: $orderStatus,
            actionCode: $actionCode,
            actionCodeDescription: $actionCodeDescription,
            errorCode: $errorCode,
            errorMessage: $errorMessage,
            params: $params
        );
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fromResponse(array $response): SatimConfirmResponse
    {
        $responseAccessor = SatimResponseAccessor::make($response);
        $paramsAccessor = SatimResponseAccessor::make($responseAccessor->getArray('params'));

        return new SatimConfirmResponse(
            expiration: $responseAccessor->getString('expiration'),
            cardholderName: $responseAccessor->getString('cardholderName'),
            depositAmount: $responseAccessor->getFloat('depositAmount', 0) / 100,
            currency: $responseAccessor->getEnum('currency', SatimCurrency::class),
            pan: $responseAccessor->getString('Pan'),
            approvalCode: $responseAccessor->getString('approvalCode'),
            authCode: $responseAccessor->getInt('authCode'),
            orderNumber: $responseAccessor->getString('OrderNumber'),
            amount: $responseAccessor->getFloat('Amount', 0) / 100,
            svfeResponse: $responseAccessor->getString('SvfeResponse'),
            orderStatus: $responseAccessor->getString('OrderStatus'),
            actionCode: $responseAccessor->getString('actionCode'),
            actionCodeDescription: $responseAccessor->getString('actionCodeDescription'),
            errorCode: $responseAccessor->getString('ErrorCode'),
            errorMessage: $responseAccessor->getString('ErrorMessage'),
            ip: $responseAccessor->getString('Ip'),
            params: [
                'udf1' => $paramsAccessor->getString('udf1'),
                'respCode' => $paramsAccessor->getString('respCode'),
                'respCode_desc' => $paramsAccessor->getString('respCode_desc'),
            ]
        );
    }
}
