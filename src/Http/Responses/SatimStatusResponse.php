<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use DateTimeImmutable;
use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\OrderStatus;
use LaravelSatim\ValueObjects\StatusResponse\Attributes;
use LaravelSatim\ValueObjects\StatusResponse\CardAuthInfo;
use LaravelSatim\ValueObjects\StatusResponse\MerchantOrderParams;

final readonly class SatimStatusResponse extends SatimAbstractResponse
{
    public function successful(): bool
    {
        return $this->errorCode() === 0 && $this->orderStatus() === OrderStatus::DEPOSITED;
    }

    public function message(): ?string
    {
        return $this->actionCodeDescription();
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

    public function currency(): ?Currency
    {
        $code = $this->string('currency');

        return $code !== null ? Currency::fromCode($code) : null;
    }

    public function date(): ?DateTimeImmutable
    {
        return $this->dateTime('date');
    }

    public function orderDescription(): ?string
    {
        return $this->string('orderDescription');
    }

    public function ip(): ?string
    {
        return $this->string('ip', 'Ip');
    }

    public function merchantOrderParams(): MerchantOrderParams
    {
        return MerchantOrderParams::fromArray($this->pairs('merchantOrderParams'));
    }

    public function attributes(): Attributes
    {
        return Attributes::fromArray($this->pairs('attributes'));
    }

    public function cardAuthInfo(): CardAuthInfo
    {
        return CardAuthInfo::fromArray($this->nested('cardAuthInfo'));
    }

    public function authDateTime(): ?DateTimeImmutable
    {
        return $this->dateTime('authDateTime');
    }

    public function terminalId(): ?string
    {
        return $this->string('terminalId');
    }

    public function authRefNum(): ?string
    {
        return $this->string('authRefNum');
    }

    public function fraudLevel(): ?int
    {
        return $this->integer('fraudLevel');
    }
}
