# Laravel Satim

[![Latest Version on Packagist](https://img.shields.io/packagist/v/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Total Downloads](https://img.shields.io/packagist/dt/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Tests](https://img.shields.io/github/actions/workflow/status/achetibi/laravel-satim/tests.yml?label=tests)](https://github.com/achetibi/laravel-satim/actions)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg?style=flat-square)](phpstan.neon)
[![License](https://img.shields.io/github/license/achetibi/laravel-satim)](LICENSE.md)

**Laravel Satim** is a clean, strongly-typed Laravel package that integrates the **SATIM / CIB** online payment gateway.
It covers the full transaction lifecycle — registration, confirmation and refund — behind pure request DTOs, immutable response objects and a real exception hierarchy.

---

## 🚀 Features

- Simple configuration via `.env`
- Register / Confirm / Refund operations
- **Immutable, typed responses** — each response owns its own data
- **Rich confirmation helpers** — `cardValid()`, `cardBalanceInsufficient()`, `status()`, `paid()`, `declined()`, …
- **Real exception hierarchy** driven by the official SATIM error codes
- Credentials injected into the HTTP client (requests stay pure data-transfer objects)
- Analysed at **PHPStan level 10** and formatted with **Pint (PSR-12)**

---

## 📦 Installation

```bash
composer require achetibi/laravel-satim
```

---

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="LaravelSatim\SatimServiceProvider" --tag="config"
```

Add the following variables to your `.env`:

```env
SATIM_USERNAME=your_username
SATIM_PASSWORD=your_password
SATIM_TERMINAL=your_terminal
SATIM_LANGUAGE=FR
SATIM_CURRENCY=DZD
SATIM_API_URL=https://test2.satim.dz/payment/rest
```

Optional HTTP client settings:

```env
# SATIM strongly recommends POST so credentials are not exposed in URLs/logs.
SATIM_HTTP_CLIENT_METHOD=POST
SATIM_HTTP_CLIENT_RETRY=3
SATIM_HTTP_CLIENT_SLEEPTIME=300

SATIM_HTTP_VERIFY_SSL=true
SATIM_HTTP_ALLOW_REDIRECTS=false
SATIM_HTTP_TIMEOUT=30
```

Disable SSL verification in development environments only. `RETRY` / `SLEEPTIME` control automatic retries (and the delay in milliseconds) for failed requests.

---

## 🧠 Usage

You can resolve the service from the container (`SatimInterface`) or use the `Satim` facade — both share the same instance.

### 1. Register a transaction

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

$response = app(SatimInterface::class)->register(SatimRegisterRequest::make(
    orderNumber: 'ORD123456',    // alphanumeric, max 10 chars, unique per transaction
    amount: 1500.00,             // amount in DZD (dinars); converted to centimes internally
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',
));

// Redirect the customer to the hosted payment page:
return redirect()->away($response->formUrl);

// Keep $response->orderId to confirm the order later.
```

For merchants enabled for **bill payment**, pass the funding type indicator (sent by
SATIM inside `jsonParams` as `fundingTypeIndicator`):

```php
use LaravelSatim\Enums\SatimFundingType;

SatimRegisterRequest::make(
    orderNumber: 'ORD123456',
    amount: 1500.00,
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',
    fundingType: SatimFundingType::BILL_PAYMENT, // "CP" (or SatimFundingType::BILL_PAYMENT_698 for "698")
);
```

### 2. Confirm a transaction

After the customer pays and is redirected back to your `returnUrl`, confirm the order.
The `mdOrder` is the gateway order identifier returned by `register` as `$response->orderId`:

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;

$response = app(SatimInterface::class)->confirm(SatimConfirmRequest::make(
    mdOrder: 'BnTjnFDzZSP97QXu8FXq',
));

if ($response->paymentAccepted()) {
    // Payment captured successfully.
} elseif ($response->cardBalanceInsufficient()) {
    // Insufficient funds.
} elseif ($response->declined()) {
    // Any other decline — inspect $response->errorMessage().
}
```

A **declined card is a business outcome, not an exception**: `confirm()` returns a
`SatimConfirmResponse` you can inspect. Available helpers include:

- Outcome: `successful()`, `fail()`, `paymentAccepted()`, `status()` (a `SatimOrderStatus` enum)
- Lifecycle: `paid()`, `approved()`, `declined()`, `reversed()`, `refunded()`, `registeredNotPaid()`
- Decline reasons: `cardValid()`, `cardTemporarilyBlocked()`, `cardLost()`, `cardStolen()`, `cardInvalidExpiryDate()`, `cardUnavailable()`, `cardLimitExceeded()`, `cardBalanceInsufficient()`, `cardInvalidCVV2()`, `cardExceededPasswordAttempts()`, `cardNotAuthorizedForOnlinePayment()`, `cardInactiveForOnlinePayment()`, `cardExpired()`, `cardExceededTransactionCeiling()`

### 3. Refund a transaction

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimRefundRequest;

$response = app(SatimInterface::class)->refund(SatimRefundRequest::make(
    orderId: 'BnTjnFDzZSP97QXu8FXq',
    amount: 1500.00,
));
```

### Using the facade

```php
use LaravelSatim\Facades\Satim;

$response = Satim::register(SatimRegisterRequest::make(/* ... */));
```

### Overriding language and currency

Language and currency default to your `.env` values. Override them **per request**
directly on the request DTO:

```php
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

SatimRegisterRequest::make(
    orderNumber: 'ORD123456',
    amount: 1500.00,
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',
    currency: SatimCurrency::DZD,
    language: SatimLanguage::AR,
);
```

---

## ⚠️ Error handling

API-level errors (invalid credentials, unknown order, system errors…) raise **typed
exceptions**; card declines during confirmation do **not** (see above).

```php
use LaravelSatim\Exceptions\SatimAuthenticationException;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Exceptions\SatimException;
use LaravelSatim\Exceptions\SatimPaymentException;
use LaravelSatim\Exceptions\SatimValidationException;

try {
    $response = app(SatimInterface::class)->register($request);
} catch (SatimValidationException $e) {
    // Invalid request data (thrown before the request is sent) — $e->errors()
} catch (SatimAuthenticationException $e) {
    // Access denied / merchant must change password (SATIM code 5)
} catch (SatimPaymentException $e) {
    // Order / payment error — $e->errorCode(), $e->context()
} catch (SatimConnectionException $e) {
    // Network / transport failure — $e->getPrevious()
} catch (SatimException $e) {
    // Base type: catch-all for any SATIM error
}
```

Exception hierarchy:

```
SatimException (base)
├── SatimConfigurationException   // missing credentials / API URL
├── SatimValidationException      // request DTO validation (->errors())
├── SatimConnectionException      // transport / HTTP failure
└── SatimResponseException        // SATIM returned an error code (->errorCode(), ->context())
    ├── SatimAuthenticationException
    └── SatimPaymentException
```

---

## ✅ Testing

```bash
composer test
```

Runs Pint (PSR-12), PHPStan (level 10) and the Pest suite. The 15 official SATIM test
cards are covered as a data-driven suite. A live sandbox harness is available under
`tests/Integration` (opt-in via `SATIM_INTEGRATION=1` and real credentials).

---

## 📌 Roadmap

- [x] Register / Confirm / Refund operations
- [x] Native request validation layer
- [x] Typed exception mapping from SATIM error codes
- [x] End-to-end test suite with fake HTTP responses
- [ ] Status operation
- [ ] Webhook support

---

## 🔒 Security

If you discover any security-related issues, please email **chetibi.abderrahim@gmail.com** instead of using the issue tracker.

---

## 🙏 Credits

- [Abderrahim CHETIBI](https://github.com/achetibi)
- [All Contributors](../../contributors)

---

## 📄 License

The MIT License (MIT).
See [LICENSE.md](LICENSE.md) for full license text.
