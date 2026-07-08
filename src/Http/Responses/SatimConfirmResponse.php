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

    public function message(): ?string
    {
        return $this->respCodeDesc() ?? $this->actionCodeDescription();
    }

    public function actionCode(): ?int
    {
        return $this->integer('actionCode');
    }

    public function actionCodeDescription(): ?string
    {
        return $this->string('actionCodeDescription');
    }

    public function amount(): ?float
    {
        return $this->money('amount', 'Amount');
    }

    public function approvalCode(): ?string
    {
        return $this->string('approvalCode');
    }

    public function authorizationResponseId(): ?string
    {
        return $this->string('authorizationResponseId');
    }

    public function currency(): ?Currency
    {
        $code = $this->string('currency');

        return $code !== null ? Currency::fromCode($code) : null;
    }

    public function depositAmount(): ?float
    {
        return $this->money('depositAmount');
    }

    public function errorCode(): int
    {
        return $this->integer('errorCode', 'ErrorCode') ?? 0;
    }

    public function errorMessage(): ?string
    {
        return $this->string('errorMessage', 'ErrorMessage');
    }

    public function orderNumber(): ?string
    {
        return $this->string('orderNumber', 'OrderNumber');
    }

    public function orderStatus(): ?OrderStatus
    {
        $value = $this->integer('orderStatus', 'OrderStatus');

        return $value !== null ? OrderStatus::tryFrom($value) : null;
    }

    public function respCode(): ?string
    {
        return $this->string('params.respCode');
    }

    public function respCodeDesc(): ?string
    {
        return $this->string('params.respCode_desc');
    }

    public function svfeResponse(): ?string
    {
        return $this->string('SvfeResponse');
    }
}
