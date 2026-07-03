<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimOrderStatus;

final readonly class SatimConfirmResponse implements SatimResponseInterface
{
    /**
     * @param  array<string, string|null>  $params
     */
    public function __construct(
        public ?string $expiration = null,
        public ?string $cardholderName = null,
        public ?float $depositAmount = null,
        public ?SatimCurrency $currency = null,
        public ?string $pan = null,
        public ?string $approvalCode = null,
        public ?string $authorizationResponseId = null,
        public ?string $orderNumber = null,
        public ?float $amount = null,
        public ?string $svfeResponse = null,
        public ?string $ip = null,
        public ?string $clientId = null,
        public ?string $bindingId = null,
        public ?string $paymentAccountReference = null,
        public ?string $description = null,
        public ?string $orderStatus = null,
        public ?string $actionCode = null,
        public ?string $actionCodeDescription = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public array $params = [],
    ) {
    }

    /**
     * @param  array<array-key, mixed>|null  $response
     */
    public static function fromResponse(?array $response): self
    {
        $response ??= [];
        $rawParams = $response['params'] ?? null;
        $params = is_array($rawParams) ? $rawParams : [];

        return new self(
            expiration: self::str($response, 'expiration'),
            cardholderName: self::str($response, 'cardholderName'),
            depositAmount: self::amount($response, 'depositAmount'),
            currency: self::currency($response),
            pan: self::str($response, 'Pan'),
            approvalCode: self::str($response, 'approvalCode'),
            authorizationResponseId: self::str($response, 'authorizationResponseId'),
            orderNumber: self::str($response, 'OrderNumber'),
            amount: self::amount($response, 'Amount'),
            svfeResponse: self::str($response, 'SvfeResponse'),
            ip: self::str($response, 'Ip'),
            clientId: self::str($response, 'clientId'),
            bindingId: self::str($response, 'bindingId'),
            paymentAccountReference: self::str($response, 'paymentAccountReference'),
            description: self::str($response, 'Description'),
            orderStatus: self::str($response, 'OrderStatus'),
            actionCode: self::str($response, 'actionCode'),
            actionCodeDescription: self::str($response, 'actionCodeDescription'),
            errorCode: self::str($response, 'ErrorCode'),
            errorMessage: self::str($response, 'ErrorMessage'),
            params: [
                'udf1' => self::str($params, 'udf1'),
                'respCode' => self::str($params, 'respCode'),
                'respCode_desc' => self::str($params, 'respCode_desc'),
            ],
        );
    }

    public function cardTemporarilyBlocked(): bool
    {
        return $this->respCode() === '37' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '203';
    }

    public function cardLost(): bool
    {
        return $this->respCode() === '41' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '208';
    }

    public function cardStolen(): bool
    {
        return $this->respCode() === '43' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '209';
    }

    public function cardInvalidExpiryDate(): bool
    {
        return $this->respCode() === 'AD' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    public function cardUnavailable(): bool
    {
        return $this->respCode() === '62' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '125';
    }

    public function cardLimitExceeded(): bool
    {
        return $this->respCode() === '61' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '121';
    }

    public function cardBalanceInsufficient(): bool
    {
        return $this->respCode() === '51' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '116';
    }

    public function cardInvalidCVV2(): bool
    {
        return $this->respCode() === 'AB' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '111';
    }

    public function cardExceededPasswordAttempts(): bool
    {
        return $this->respCode() === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    public function cardNotAuthorizedForOnlinePayment(): bool
    {
        return $this->respCode() === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    public function cardInactiveForOnlinePayment(): bool
    {
        return $this->respCode() === 'AE' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    public function cardValid(): bool
    {
        return $this->respCode() === '00' && $this->errorCode === '0' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    public function cardExpired(): bool
    {
        return $this->respCode() === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    public function cardExceededTransactionCeiling(): bool
    {
        return $this->respCode() === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    public function status(): ?SatimOrderStatus
    {
        return $this->orderStatus === null ? null : SatimOrderStatus::tryFrom((int) $this->orderStatus);
    }

    public function registeredNotPaid(): bool
    {
        return $this->status() === SatimOrderStatus::RegisteredNotPaid;
    }

    public function approved(): bool
    {
        return $this->status() === SatimOrderStatus::Approved;
    }

    public function paid(): bool
    {
        return $this->status() === SatimOrderStatus::Deposited;
    }

    public function reversed(): bool
    {
        return $this->status() === SatimOrderStatus::Reversed;
    }

    public function refunded(): bool
    {
        return $this->status() === SatimOrderStatus::Refunded;
    }

    public function declined(): bool
    {
        return $this->status() === SatimOrderStatus::AuthorizationDeclined
            || $this->status() === SatimOrderStatus::Declined;
    }

    public function paymentAccepted(): bool
    {
        return $this->respCode() === '00' && $this->errorCode === '0' && $this->paid();
    }

    public function successful(): bool
    {
        return $this->paid() || $this->approved();
    }

    public function fail(): bool
    {
        return $this->declined() || $this->reversed();
    }

    public function errorMessage(): ?string
    {
        return ($this->params['respCode_desc'] ?? null) ?? ($this->actionCodeDescription ?: null);
    }

    public function successMessage(): ?string
    {
        return ($this->params['respCode_desc'] ?? null) ?? ($this->actionCodeDescription ?: null);
    }

    private function respCode(): ?string
    {
        return $this->params['respCode'] ?? null;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function str(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function amount(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value / 100 : null;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function currency(array $data): ?SatimCurrency
    {
        $value = self::str($data, 'currency');

        return $value === null ? null : SatimCurrency::tryFrom($value);
    }
}
