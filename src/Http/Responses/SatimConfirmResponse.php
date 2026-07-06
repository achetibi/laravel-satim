<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\OrderStatus;

final readonly class SatimConfirmResponse extends SatimAbstractResponse
{
    public function successful(): bool
    {
        return $this->errorCode() === 0 && $this->orderStatus() === OrderStatus::DEPOSITED;
    }

    public function failureMessage(): ?string
    {
        return $this->respCodeDesc() ?? $this->actionCodeDescription();
    }

    public function actionCode(): ?int
    {
        $value = $this->data['actionCode'] ?? null;

        return is_int($value) ? $value : null;
    }

    public function actionCodeDescription(): ?string
    {
        $value = $this->data['actionCodeDescription'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function amount(): ?float
    {
        $value = $this->data['Amount'] ?? null;

        return is_numeric($value) ? (float) ($value / 100) : null;
    }

    public function approvalCode(): ?string
    {
        $value = $this->data['approvalCode'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function authorizationResponseId(): ?string
    {
        $value = $this->data['authorizationResponseId'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function currency(): ?Currency
    {
        $value = $this->data['currency'] ?? null;

        return is_string($value) ? Currency::fromCode($value) : null;
    }

    public function depositAmount(): ?float
    {
        $value = $this->data['depositAmount'] ?? null;

        return is_numeric($value) ? (float) ($value/100) : null;
    }

    public function errorCode(): int
    {
        $value = $this->data['errorCode'] ?? $this->data['ErrorCode'] ?? null;

        return is_int($value) ? $value : 0;
    }

    public function errorMessage(): ?string
    {
        $value = $this->data['errorMessage'] ?? $this->data['ErrorMessage'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function orderNumber(): ?string
    {
        $value = $this->data['OrderNumber'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function orderStatus(): ?OrderStatus
    {
        $value = $this->data['OrderStatus'] ?? null;

        return is_int($value) ? OrderStatus::tryFrom($value) : null;
    }

    public function respCode(): ?string
    {
        $value = $this->data['params']['respCode'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function respCodeDesc(): ?string
    {
        $value = $this->data['params']['respCode_desc'] ?? null;

        return is_string($value) ? $value : null;
    }

    public function svfeResponse(): ?string
    {
        $value = $this->data['SvfeResponse'] ?? null;

        return is_string($value) ? $value : null;
    }
}
