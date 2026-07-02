<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Enums\SatimCurrency;

class SatimConfirmResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    /**
     * @param  array<string, mixed>  $params
     */
    public function __construct(
        public readonly ?string $expiration = null,
        public readonly ?string $cardholderName = null,
        public readonly ?float $depositAmount = null,
        public readonly ?SatimCurrency $currency = null,
        public readonly ?string $pan = null,
        public readonly ?string $approvalCode = null,
        public readonly ?string $authorizationResponseId = null,
        public readonly ?string $orderNumber = null,
        public readonly ?float $amount = null,
        public readonly ?string $svfeResponse = null,
        public readonly ?string $ip = null,
        public readonly ?string $clientId = null,
        public readonly ?string $bindingId = null,
        public readonly ?string $paymentAccountReference = null,
        public readonly ?string $description = null,
        ?string $orderStatus = null,
        ?string $actionCode = null,
        ?string $actionCodeDescription = null,
        ?string $errorCode = null,
        ?string $errorMessage = null,
        array $params = []
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
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): SatimConfirmResponse
    {
        $data = SatimResponseData::from($response);

        return new SatimConfirmResponse(
            expiration: $data->string('expiration'),
            cardholderName: $data->string('cardholderName'),
            depositAmount: $data->float('depositAmount', 0.0) / 100,
            currency: $data->enum('currency', SatimCurrency::class),
            pan: $data->string('Pan'),
            approvalCode: $data->string('approvalCode'),
            authorizationResponseId: $data->string('authorizationResponseId'),
            orderNumber: $data->string('OrderNumber'),
            amount: $data->float('Amount', 0.0) / 100,
            svfeResponse: $data->string('SvfeResponse'),
            ip: $data->string('Ip'),
            clientId: $data->string('clientId'),
            bindingId: $data->string('bindingId'),
            paymentAccountReference: $data->string('paymentAccountReference'),
            description: $data->string('Description'),
            orderStatus: $data->string('OrderStatus'),
            actionCode: $data->string('actionCode'),
            actionCodeDescription: $data->string('actionCodeDescription'),
            errorCode: $data->string('ErrorCode'),
            errorMessage: $data->string('ErrorMessage'),
            params: [
                'udf1' => $data->string('params.udf1'),
                'respCode' => $data->string('params.respCode'),
                'respCode_desc' => $data->string('params.respCode_desc'),
            ]
        );
    }
}
