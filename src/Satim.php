<?php

declare(strict_types=1);

namespace LaravelSatim;

use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimApiServerException;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Http\SatimHttpClient;

class Satim implements SatimInterface
{
    public function __construct(
        protected SatimHttpClient $httpClient,
        protected ?SatimCurrency $currency = null,
        protected ?SatimLanguage $language = null,
    ) {
    }

    /**
     * @throws SatimApiServerException
     */
    public function register(SatimRegisterRequest $request): SatimRegisterResponse
    {
        $data = $this->data($request->toRequest());

        return SatimRegisterResponse::fromResponse(
            $this->httpClient->call('/register.do', $data)
        );
    }

    /**
     * @throws SatimApiServerException
     */
    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse
    {
        $data = $this->data($request->toRequest());

        return SatimConfirmResponse::fromResponse(
            $this->httpClient->call('/public/acknowledgeTransaction.do', $data)
        );
    }

    /**
     * @throws SatimApiServerException
     */
    public function refund(SatimRefundRequest $request): SatimRefundResponse
    {
        $data = $this->data($request->toRequest());

        return SatimRefundResponse::fromResponse(
            $this->httpClient->call('/refund.do', $data)
        );
    }

    public function setCurrency(SatimCurrency $currency): SatimInterface
    {
        $this->currency = $currency;

        return $this;
    }

    public function setLanguage(SatimLanguage $language): SatimInterface
    {
        $this->language = $language;

        return $this;
    }

    public function getCurrency(): SatimCurrency
    {
        if (! ($this->currency instanceof SatimCurrency)) {
            $value = config('satim.currency');
            $this->currency = SatimCurrency::resolve(is_string($value) ? $value : null)
                ?? SatimCurrency::fallback();
        }

        return $this->currency;
    }

    public function getLanguage(): SatimLanguage
    {
        if (! ($this->language instanceof SatimLanguage)) {
            $value = config('satim.language');
            $this->language = SatimLanguage::resolve(is_string($value) ? $value : null)
                ?? SatimLanguage::fallback();
        }

        return $this->language;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function data(array $data): array
    {
        if (array_key_exists('language', $data) && empty($data['language'])) {
            $data['language'] = $this->getLanguage()->value;
        }

        if (array_key_exists('currency', $data) && empty($data['currency'])) {
            $data['currency'] = $this->getCurrency()->value;
        }

        return $data;
    }
}
