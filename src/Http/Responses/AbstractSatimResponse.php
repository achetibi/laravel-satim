<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

abstract class AbstractSatimResponse
{
    public function __construct(
        public readonly ?string $orderStatus = null,
        public readonly ?string $actionCode = null,
        public readonly ?string $actionCodeDescription = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
        public readonly array $params = []
    ) {
    }

    public function cardTemporarilyBlocked(): bool
    {
        return ($this->params['respCode'] ?? null) === '37' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '203';
    }

    public function cardLost(): bool
    {
        return ($this->params['respCode'] ?? null) === '41' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '208';
    }

    public function cardStolen(): bool
    {
        return ($this->params['respCode'] ?? null) === '43' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '209';
    }

    public function cardInvalidExpiryDate(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AD' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    public function cardUnavailable(): bool
    {
        return ($this->params['respCode'] ?? null) === '62' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '125';
    }

    public function cardLimitExceeded(): bool
    {
        return ($this->params['respCode'] ?? null) === '61' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '121';
    }

    public function cardBalanceInsufficient(): bool
    {
        return ($this->params['respCode'] ?? null) === '51' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '116';
    }

    public function cardInvalidCVV2(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AB' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '111';
    }

    public function cardExceededPasswordAttempts(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    public function cardNotAuthorizedForOnlinePayment(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    public function cardInactiveForOnlinePayment(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AE' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    public function cardValid(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    public function cardExpired(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    public function cardExceededTransactionCeiling(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    public function paymentRegistered(): bool
    {
        return $this->errorMessage === null && $this->errorCode === '0';
    }

    public function paymentConfirmed(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '2' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    public function paymentAccepted(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '2';
    }

    public function paymentRejected(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '3';
    }

    public function paymentRefunded(): bool
    {
        return $this->orderStatus === '4';
    }

    public function paymentCancelled(): bool
    {
        return $this->errorCode === '3' && $this->actionCode === '10';
    }

    public function errorMessage(): ?string
    {
        return $this->params['respCode_desc'] ?? ($this->actionCodeDescription ?: null);
    }

    public function errorCode(): ?string
    {
        return ($this->params['respCode'] ?? null) ?: $this->errorCode ?: null;
    }

    public function successMessage(): ?string
    {
        return $this->params['respCode_desc'] ?? ($this->actionCodeDescription ?: null);
    }

    public function successful(): bool
    {
        return $this->orderStatus === '0' || $this->orderStatus === '2' || $this->errorCode === '0';
    }

    public function fail(): bool
    {
        return $this->orderStatus !== '0' && $this->orderStatus !== '2' && $this->orderStatus !== '4' && $this->errorCode !== '0';
    }
}
