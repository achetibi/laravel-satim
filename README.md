# Laravel Satim

[![Latest Version on Packagist](https://img.shields.io/packagist/v/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Total Downloads](https://img.shields.io/packagist/dt/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Tests](https://img.shields.io/github/actions/workflow/status/achetibi/laravel-satim/tests.yml?label=tests)](https://github.com/achetibi/laravel-satim/actions)
[![License](https://img.shields.io/github/license/achetibi/laravel-satim)](LICENSE.md)

**Laravel Satim** is a clean, extensible Laravel package that provides seamless integration with the **Satim** online payment gateway.  
It supports full transaction lifecycle handling: registration, confirmation, and refund, all wrapped in a service-oriented architecture.

---

## 🚀 Features

- Simple configuration via `.env`
- Clean service classes for payment flow:
    - Register
    - Confirm
    - Refund
- Built-in error handling and response abstraction
- Custom HTTP client wrapper using Laravel's `Http` facade
- Laravel-native setup with service provider, config, and facade

---

## 📦 Installation

```bash
composer require achetibi/laravel-satim
```

---

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="LaravelSatim\SatimServiceProvider"
```

Add the following variables to your `.env`:

```env
SATIM_USERNAME=your_username
SATIM_PASSWORD=your_password
SATIM_TERMINAL=your_terminal
SATIM_TIMEOUT=15
SATIM_LANGUAGE=fr
SATIM_CURRENCY=DZD
SATIM_API_URL=https://fake.satim.dz/payment/rest
```

---

## 🧠 Basic Usage

### Register a transaction

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

$response = app(SatimInterface::class)->register(SatimRegisterRequest::make(
    orderNumber: 'ORD-123456',
    amount: 1500,
    returnUrl: route('payment.success'),
    udf1: 'ORD-123456'
));
```

### Confirm a transaction

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;

$response = app(SatimInterface::class)->confirm(SatimConfirmRequest::make(
    orderId: 'BnTjnFDzZSP97QXu8FXq'
));
```

### Refund a transaction

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Http\Requests\SatimRefundRequest;

$response = app(SatimInterface::class)->refund(SatimRefundRequest::make(
    orderId: 'BnTjnFDzZSP97QXu8FXq',
    amount: 1500
));
```

All services return typed response or throw custom exceptions for errors.

---

### Overriding language and currency (optional)

By default, the values for language and currency are loaded from your `.env` file.  
If you need to override them on a per-request basis, you can call `setLanguage()` and `setCurrency()` on the service before executing the request:

```php
use LaravelSatim\Contracts\SatimInterface;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Enums\SatimCurrency;

$service = app(SatimInterface::class)
    ->setLanguage(SatimLanguage::AR)
    ->setCurrency(SatimCurrency::DZD);

$response = $service->register(SatimRegisterRequest::make(
    orderNumber: 'ORD-123456',
    amount: 1500,
    returnUrl: route('payment.success'),
    udf1: 'ORD-123456'
));
```

## ✅ Testing

Run the test suite:

```bash
composer test
```

---

## 📌 Roadmap

- [x] Register / Confirm / Refund operations
- [x] Request / Response validation layer
- [x] Exception mapping
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
