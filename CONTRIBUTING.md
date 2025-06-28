# Contributing to Laravel Satim

First off, thanks for taking the time to contribute! 🚀

The following is a set of guidelines for contributing to this Laravel package.
These are mostly guidelines, not rules. Use your best judgment.

---

## 📦 Setup

1. Fork the repository and clone it locally.
2. Install dependencies:

```bash
composer install
```

3. Run the test suite to ensure everything works:

```bash
./vendor/bin/pest
```

You’re now ready to work.

---

## 🧪 Tests

All features and bug fixes **must** be covered by tests.
We use [Pest](https://pestphp.com/) for testing.

Run tests with:

```bash
./vendor/bin/pest
```

> Tests must pass before any PR is merged.

---

## 📁 Code Style

This package uses **Laravel Pint** with the `laravel` preset.

Format your code before submitting:

```bash
./vendor/bin/pint
```

> CI will reject non-formatted code.

---

## ✅ Pull Requests

- PRs should be small, focused, and self-contained.
- Clearly describe **what** the change does and **why**.
- Reference related issues using `Fixes #123` or `Closes #45` if applicable.
- Include test coverage and/or documentation when needed.

---

## 📄 Commits

Follow [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) if possible:

```
feat: add refund feature
fix: handle timeout edge case in SatimHttpClient
test: cover invalid response from API
```

---

## 🙏 Code of Conduct

Be respectful. We're here to build good software, not egos.

---

## 💬 Questions?

Open an issue or start a discussion in the repo if you're unsure.

Thanks again! 🙌
