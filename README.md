# Laravel Wide Events

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Zamion101/laravel-wide-events.svg?style=flat-square)](https://packagist.org/packages/Zamion101/laravel-wide-events)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Zamion101/laravel-wide-events/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Zamion101/laravel-wide-events/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/Zamion101/laravel-wide-events/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/Zamion101/laravel-wide-events/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/Zamion101/laravel-wide-events.svg?style=flat-square)](https://packagist.org/packages/Zamion101/laravel-wide-events)

Laravel implementation of Wide Events "Canonical Logs"

## Installation

You can install the package via composer:

```bash
composer require Zamion101/laravel-wide-events
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-wide-events-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-wide-events-config"
```

This is the contents of the published config file:

```php
return [
];
```
## Usage

```php
$laravelWideEvents = new Zamion101\WideEvents();
echo $laravelWideEvents->echoPhrase('Hello, Zamion101!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Zamion101](https://github.com/Zamion101)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
