# Changelog

All notable changes to `laravel-satim` will be documented in this file

## 2.0.1 - 2026-07-07

Full rewrite around pure request DTOs, immutable typed responses and a real exception hierarchy.
This is a **breaking** release; see the migration notes below.

### Breaking Changes

- renamed the service contract `Contracts\SatimInterface` to `Contracts\SatimGatewayInterface`; the core
  `Satim` class is now `SatimGateway` and the facade moved from `SatimFacade` to `Facades\Satim`
- renamed enums `SatimCurrency` → `Currency` and `SatimLanguage` → `Language`, and added `Environment`,
  `HttpMethod`, `FundingType` and `OrderStatus`
- replaced the exception set (`SatimApiServerException`, `SatimAuthenticationException`,
  `SatimInvalidArgumentException`, `SatimInvalidConfigException`, `SatimInvalidOrderException`) with a single
  hierarchy: `SatimAbstractException`, `SatimConfigurationException`, `SatimConnectionException`,
  `SatimEncodingException`, `SatimResponseException`, `SatimValidationException`
- renamed the request/response base classes (`AbstractSatimRequest` → `SatimAbstractRequest`,
  `AbstractSatimResponse` → `SatimAbstractResponse`) and moved the HTTP client to `Client\SatimHttpClient`
- requests are now constructed with `new` and named arguments instead of a `::make()` factory
- restructured the configuration: per-environment `base_urls` (replacing `SATIM_API_URL`), a config-driven
  `http.method`, and new `http.retries` / `http.retry_delay` / `logging` sections
- removed `Support\SatimResponseAccessor` and `Traits\EnumToArray`

### Added

- `SatimValidator` request-validation layer with package-translated messages
- `SatimConfig` typed configuration accessor
- config-driven HTTP method (`http.method`), defaulting to POST as recommended by SATIM
- automatic retries for transport failures and 5xx responses (`http.retries`, `http.retry_delay`)
- optional request logging (`logging.enabled`, `logging.channel`)
- Arabic, English and French translations with automatic fallback to the package default locale when the
  active Laravel locale is unsupported
- fully documented `config/satim.php`
- explicit `guzzlehttp/guzzle` dependency
- complete unit test suite mirroring the `src` structure
- CI matrix across PHP 8.3/8.4 and Laravel 11/12/13 (lowest and stable dependencies)

### Changed

- de-duplicated the response accessors behind typed helpers on `SatimAbstractResponse`
- analysed at PHPStan level 10 and formatted with Pint (PSR-12)

## 1.1.2 - 2026-04-14

- added support for Laravel 13
- updated dependencies to the latest versions

## 1.1.1 - 2025-12-02

- catch any throwable exception in the satim http client

## 1.1.0 - 2025-11-03

- added satim http client config
- added http options config

## 1.0.1 - 2025-10-18

- fixed facade binding

## 1.0.0 - 2025-06-17

- initial release

[2.0.0]: https://github.com/achetibi/laravel-satim/compare/v1.1.2...v2.0.0
[1.1.2]: https://github.com/achetibi/laravel-satim/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/achetibi/laravel-satim/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/achetibi/laravel-satim/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/achetibi/laravel-satim/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/achetibi/laravel-satim/releases/tag/v1.0.0
