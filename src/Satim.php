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

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name Satim
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class Satim implements SatimInterface
{
    public function __construct(
        protected SatimHttpClient $httpClient,
        protected ?SatimCurrency $currency = null,
        protected ?SatimLanguage $language = null,
    ) {}

    /**
     * @throws SatimApiServerException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
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
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function confirm(SatimConfirmRequest $request): SatimConfirmResponse
    {
        $data = $this->data($request->toRequest());

        return SatimConfirmResponse::fromResponse(
            $this->httpClient->call('/confirmOrder.do', $data)
        );
    }

    /**
     * @throws SatimApiServerException
     *
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function refund(SatimRefundRequest $request): SatimRefundResponse
    {
        $data = $this->data($request->toRequest());

        return SatimRefundResponse::fromResponse(
            $this->httpClient->call('/refund.do', $data)
        );
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function setCurrency(SatimCurrency $currency): SatimInterface
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function setLanguage(SatimLanguage $language): SatimInterface
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function getCurrency(): SatimCurrency
    {
        if (! ($this->currency instanceof SatimCurrency)) {
            $this->currency = SatimCurrency::tryFrom(strtoupper(config('satim.currency')))
                ?? SatimCurrency::fallback();
        }

        return $this->currency;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function getLanguage(): SatimLanguage
    {
        if (! ($this->language instanceof SatimLanguage)) {
            $this->language = SatimLanguage::tryFrom(strtoupper(config('satim.language')))
                ?? SatimLanguage::fallback();
        }

        return $this->language;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
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
