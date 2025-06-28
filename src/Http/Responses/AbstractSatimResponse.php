<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name AbstractSatimResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
abstract class AbstractSatimResponse
{
    public function __construct(
        public ?string $orderStatus = null,
        public ?string $actionCode = null,
        public ?string $actionCodeDescription = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public array $params = []
    ) {}

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardTemporarilyBlocked(): bool
    {
        return ($this->params['respCode'] ?? null) === '37' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '203';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardLost(): bool
    {
        return ($this->params['respCode'] ?? null) === '41' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '208';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardStolen(): bool
    {
        return ($this->params['respCode'] ?? null) === '43' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '209';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardInvalidExpiryDate(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AD' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardUnavailable(): bool
    {
        return ($this->params['respCode'] ?? null) === '62' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '125';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardLimitExceeded(): bool
    {
        return ($this->params['respCode'] ?? null) === '61' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '121';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardBalanceInsufficient(): bool
    {
        return ($this->params['respCode'] ?? null) === '51' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '116';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardInvalidCVV2(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AB' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '111';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardExceededPasswordAttempts(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardNotAuthorizedForOnlinePayment(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '2003';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardInactiveForOnlinePayment(): bool
    {
        return ($this->params['respCode'] ?? null) === 'AE' && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-1';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardValid(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardExpired(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function cardExceededTransactionCeiling(): bool
    {
        return ($this->params['respCode'] ?? null) === null && $this->errorCode === '3' && $this->orderStatus === '6' && $this->actionCode === '-2006';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentRegistered(): bool
    {
        return $this->errorMessage === null && $this->errorCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentConfirmed(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '2' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentAccepted(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '2';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentRejected(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '3';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentRefunded(): bool
    {
        return $this->orderStatus === '4';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function paymentCancelled(): bool
    {
        return $this->errorCode === '3' && $this->actionCode === '10';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function errorMessage(): ?string
    {
        return $this->params['respCode_desc'] ?? ($this->actionCodeDescription ?: null);
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function errorCode(): ?string
    {
        return ($this->params['respCode'] ?? null) ?: $this->errorCode ?: null;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function successMessage(): ?string
    {
        return $this->params['respCode_desc'] ?? ($this->actionCodeDescription ?: null);
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function successful(): bool
    {
        return $this->orderStatus === '0' || $this->orderStatus === '2' || $this->errorCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function fail(): bool
    {
        return $this->orderStatus !== '0' && $this->orderStatus !== '2' && $this->orderStatus !== '4' && $this->errorCode !== '0';
    }
}
