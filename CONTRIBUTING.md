# Contributing to jak-travel-sys

Thank you for your interest in contributing! Please follow these guidelines to help us maintain a high-quality project.

## 🛠️ How to Contribute
- Fork the repository and create your branch from `main`.
- Write clear, descriptive commit messages.
- Ensure your code follows the style guides (see below).
- Add or update tests for new features or bug fixes.
- Update documentation as needed.
- Open a pull request and describe your changes.

## 🧹 Code Style
- **PHP:** Use [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer). Run `vendor/bin/php-cs-fixer fix` before submitting.
- **JS/TS:** Use [Prettier](https://prettier.io/) and (optionally) [ESLint](https://eslint.org/). Run `npm run prettier:check` and `npx eslint .`.

## 🔒 Security
- Never commit secrets or credentials. Use `.env.example` as a template.
- Report vulnerabilities via [SECURITY.md](SECURITY.md).

## 🧪 Tests
- Run all tests with `php artisan test` before submitting.
- For frontend, run `npm run test` if available.
- Ensure new features are covered by tests.

## 📦 Dependency Management
- Use Composer for PHP dependencies and npm for JS dependencies.
- Do not commit `vendor/` or `node_modules/`.

## 📄 Changelog
- Add a summary of your changes to [CHANGELOG.md](CHANGELOG.md) if relevant.

## 🤝 Code of Conduct
- Be respectful and inclusive.
- See [SECURITY.md](SECURITY.md) for responsible disclosure.

---

Thank you for helping us improve jak-travel-sys!
