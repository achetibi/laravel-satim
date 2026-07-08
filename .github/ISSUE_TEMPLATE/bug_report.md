---
name: Bug report
about: Report a problem with the laravel-satim package
title: '[Bug]: '
labels: bug
assignees: ''

---

**Describe the bug**
A clear and concise description of what the bug is and which gateway operation is affected
(register / confirm / refund).

**To reproduce**
Provide a minimal code sample that triggers the problem:

```php
use LaravelSatim\Facades\Satim;
use LaravelSatim\Http\Requests\SatimRegisterRequest;

$response = Satim::register(new SatimRegisterRequest(
    orderNumber: 'ORD123456',
    amount: 1500.00,
    returnUrl: 'https://example.test/return',
    udf1: 'ORD123456',
));
```

**Expected behavior**
A clear and concise description of what you expected to happen.

**Actual behavior**
What actually happened. Include the exception class and message, or the raw gateway response
(`$response->raw()`) if relevant.

**Exception / stack trace**
<!-- Paste the full exception message and stack trace here, if any. Redact any credentials. -->

```
```

**Environment**
- Package version: <!-- e.g. 2.0.0 -->
- PHP version: <!-- e.g. 8.3 -->
- Laravel version: <!-- e.g. 11.x -->
- SATIM environment: <!-- testing / staging / production -->
- HTTP method (`satim.http.method`): <!-- POST (default) / GET -->

**Configuration (redacted)**
<!-- Relevant, non-secret parts of your config/satim.php. NEVER paste your username, password or terminal id. -->

```php
```

**Additional context**
Add any other context about the problem here.

> ⚠️ Do not include real credentials, card data, or full gateway responses containing sensitive
> information. For security vulnerabilities, please email chetibi.abderrahim@gmail.com instead of
> opening a public issue.
