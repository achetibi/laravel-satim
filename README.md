# Laravel Satim

[![Latest Version on Packagist](https://img.shields.io/packagist/v/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Total Downloads](https://img.shields.io/packagist/dt/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Tests](https://img.shields.io/github/actions/workflow/status/achetibi/laravel-satim/tests.yml?label=tests)](https://github.com/achetibi/laravel-satim/actions)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg?style=flat-square)](phpstan.neon)
[![License](https://img.shields.io/github/license/achetibi/laravel-satim)](LICENSE.md)

**Laravel Satim** is a clean, strongly-typed Laravel package for **SATIM online payments in Algeria** — the
interbank e-payment gateway behind **CIB** and **Edahabia** card payments (paiement en ligne / الدفع الإلكتروني).
It covers the full transaction lifecycle — registration, confirmation and refund — behind pure request DTOs, immutable
response objects and a real exception hierarchy.

> Keywords: SATIM Laravel, CIB Laravel, e-paiement Algérie, Algerian payment gateway, carte CIB / Edahabia, DZ.

---

## 🚀 Features

- Simple configuration via `.env`
- Register / Confirm / Refund operations
- **Immutable, typed responses** — each response owns its own data
- **Real exception hierarchy** driven by the official SATIM error codes
- **Config-driven HTTP method** — POST by default, as recommended by SATIM
- Automatic retries for transport failures and 5xx responses
- Credentials injected into the HTTP client (requests stay pure data-transfer objects)
- Translations in **Arabic, English and French** with automatic fallback
- Analysed at **PHPStan level 10** and formatted with **Pint (PSR-12)**

---

## 📦 Requirements

- PHP 8.3+
- Laravel 11, 12 or 13

## 📦 Installation

```bash
composer require achetibi/laravel-satim
```

---

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="LaravelSatim\SatimServiceProvider" --tag="satim-config"
```

Optionally publish the translations:

```bash
php artisan vendor:publish --provider="LaravelSatim\SatimServiceProvider" --tag="satim-lang"
```

Add the following variables to your `.env`:

```env
SATIM_ENV=test
SATIM_USERNAME=your_username
SATIM_PASSWORD=your_password
SATIM_TERMINAL=your_terminal
SATIM_CURRENCY=DZD
SATIM_LANGUAGE=fr
```

Optional HTTP client settings (defaults shown):

```env
# SATIM strongly recommends POST so credentials are never exposed in URLs/logs.
SATIM_HTTP_METHOD=POST
SATIM_TIMEOUT=30
SATIM_CONNECT_TIMEOUT=10
SATIM_RETRIES=2
SATIM_RETRY_DELAY=300
SATIM_SSL_VERIFY=true

# Optional request logging (endpoint/method/status only, never credentials)
SATIM_LOG=false
SATIM_LOG_CHANNEL=stack
```

Disable SSL verification in local development only. `SATIM_RETRIES` / `SATIM_RETRY_DELAY` control the number of
automatic retries and the linear back-off delay (in milliseconds) for transport failures and 5xx responses.

Every configuration option is documented inline in [`config/satim.php`](config/satim.php).

---

## 🧠 Usage

Resolve the gateway from the container via `SatimGatewayInterface`, or use the `Satim` facade — both share the same
singleton instance.

### 1. Register a transaction

```php
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

$response = app(SatimGatewayInterface::class)->register(new SatimRegisterRequest(
    orderNumber: 'ORD123456',    // alphanumeric, max 10 chars, unique per transaction
    amount: 1500.00,             // amount in DZD (dinars); converted to centimes internally
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',           // required user-defined field
));

// Redirect the customer to the hosted payment page:
return redirect()->away($response->formUrl());

// Keep $response->orderId() to confirm the order later.
```

For merchants enabled for **bill payment**, pass the funding type indicator (sent by SATIM inside `jsonParams` as
`fundingTypeIndicator`):

```php
use LaravelSatim\Enums\FundingType;

new SatimRegisterRequest(
    orderNumber: 'ORD123456',
    amount: 1500.00,
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',
    fundingType: FundingType::BILL_PAYMENT, // "CP" (or FundingType::BILL_PAYMENT_698 for "698")
);
```

`SatimRegisterRequest` also accepts the optional `udf2`–`udf5`, `failUrl`, `description`, `currency` and `language`
arguments. `SatimRegisterResponse` exposes `successful()`, `errorCode()`, `errorMessage()`, `orderId()`, `formUrl()`
and `raw()`.

### 2. Confirm a transaction

After the customer pays and is redirected back to your `returnUrl`, confirm the order. `mdOrder` is the gateway order
identifier returned by `register()` as `$response->orderId()`:

```php
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;

$response = app(SatimGatewayInterface::class)->confirm(new SatimConfirmRequest(
    mdOrder: 'BnTjnFDzZSP97QXu8FXq',
));

if ($response->successful()) {
    // Payment captured (OrderStatus::DEPOSITED).
} else {
    // Inspect the outcome — $response->orderStatus(), $response->message().
}
```

`SatimConfirmResponse` exposes rich accessors: `successful()`, `orderStatus()` (a `LaravelSatim\Enums\OrderStatus`
enum), `message()`, `amount()`, `depositAmount()`, `currency()`, `approvalCode()`, `respCode()`,
`respCodeDesc()`, `errorCode()`, `errorMessage()` and `raw()`.

### 3. Refund a transaction

```php
use LaravelSatim\Contracts\SatimGatewayInterface;
use LaravelSatim\Http\Requests\SatimRefundRequest;

$response = app(SatimGatewayInterface::class)->refund(new SatimRefundRequest(
    orderId: 'BnTjnFDzZSP97QXu8FXq',
    amount: 1500.00,
));
```

### Using the facade

```php
use LaravelSatim\Facades\Satim;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

$response = Satim::register(new SatimRegisterRequest(/* ... */));
```

### Overriding language and currency

Language and currency default to your configuration. Override them **per request** directly on the request DTO:

```php
use LaravelSatim\Enums\Currency;
use LaravelSatim\Enums\Language;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

new SatimRegisterRequest(
    orderNumber: 'ORD123456',
    amount: 1500.00,
    returnUrl: route('payment.success'),
    udf1: 'ORD123456',
    currency: Currency::DZD,
    language: Language::ARABIC,
);
```

---

## 🌍 Translations

The package ships translations for **Arabic (`ar`)**, **English (`en`)** and **French (`fr`)**. Package texts (error
messages and validation messages) follow the active Laravel locale. When the active locale is not one of the three
supported locales, the package falls back to the locale configured in `satim.defaults.language` (and ultimately to
English), so end users never see raw translation keys.

---

## ⚠️ Error handling

API-level errors (invalid credentials, unknown order, system errors…) raise **typed exceptions**. A declined card
during confirmation is a **business outcome**, not an exception: `confirm()` returns a `SatimConfirmResponse` you
inspect with `successful()` / `orderStatus()` / `message()`.

```php
use LaravelSatim\Exceptions\SatimAbstractException;
use LaravelSatim\Exceptions\SatimConnectionException;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Exceptions\SatimValidationException;

try {
    $response = app(SatimGatewayInterface::class)->register($request);
} catch (SatimValidationException $e) {
    // Invalid request data (thrown before the request is sent) — $e->errors(), $e->first()
} catch (SatimResponseException $e) {
    // SATIM returned an error code — $e->errorCode, $e->errorMessage
} catch (SatimConnectionException $e) {
    // Network / transport failure — $e->getPrevious()
} catch (SatimAbstractException $e) {
    // Base type: catch-all for any SATIM error
}
```

Exception hierarchy:

```
SatimAbstractException (base)
├── SatimConfigurationException   // missing credentials / invalid config
├── SatimValidationException      // request DTO validation (->errors(), ->first(), ->messages())
├── SatimConnectionException      // transport / HTTP failure
├── SatimEncodingException        // failed to JSON-encode jsonParams
└── SatimResponseException        // SATIM returned an error code (->errorCode, ->errorMessage)
```

---

## ✅ Testing

```bash
composer test
```

Runs Pint (PSR-12), PHPStan (level 10) and the Pest unit suite. The suite mirrors the `src` directory structure under
`tests/Unit`. Continuous integration runs the full matrix of PHP 8.3/8.4 against Laravel 11, 12 and 13, on both the
lowest and the latest resolvable dependencies.

---

## 📌 Roadmap

- [x] Register / Confirm / Refund operations
- [x] Native request validation layer
- [x] Typed exception mapping from SATIM error codes
- [x] Config-driven HTTP method and automatic retries
- [x] Full unit test suite mirroring `src`
- [ ] Status operation
- [ ] Webhook support

---

## 🔒 Security

If you discover any security-related issues, please email **chetibi.abderrahim@gmail.com** instead of using the issue
tracker.

---

## 🙏 Credits

- [Abderrahim CHETIBI](https://github.com/achetibi)
- [All Contributors](../../contributors)

---

## 📄 License

The MIT License (MIT).
See [LICENSE.md](LICENSE.md) for full license text.
