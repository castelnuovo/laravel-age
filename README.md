<p align="center">
  <img src="https://assets.castelnuovo.dev/logo.svg" width="100" />
</p>

<h1 align="center">
  castelnuovo/laravel-age
</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/castelnuovo/laravel-age.svg?style=flat-square)](https://packagist.org/packages/castelnuovo/laravel-age)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/castelnuovo/laravel-age/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/castelnuovo/laravel-age/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/castelnuovo/laravel-age/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/castelnuovo/laravel-age/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/castelnuovo/laravel-age.svg?style=flat-square)](https://packagist.org/packages/castelnuovo/laravel-age)

Laravel wrapper for the AGE cli

## Installation

> [!IMPORTANT]
> Make sure you have the AGE cli installed on your system.
> You can find the installation instructions [here](https://github.com/FiloSottile/age#installation).

You can install the package via composer:

```bash
composer require castelnuovo/laravel-age
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-age-config"
```

This is the contents of the published config file:

```php
return [
    'identity' => env('AGE_IDENTITY')
];
```

## Usage

```php
use Castelnuovo\LaravelAge;

$message = 'Hello World!';

$age = LaravelAge::generateKeypair();
$privateKey = $age->getPrivateKey();
$publicKey = $age->getPublicKey();

$age2 = new LaravelAge(publicKey: $publicKey);
$encrypted_message = $age2->encrypt($message);

$age3 = new LaravelAge(privateKey: $privateKey);
$decrypted_message = $age3->decrypt($encrypted_message);

echo $message === $decrypted_message ? 'Success' : 'Failed';
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

-   [Luca Castelnuovo](https://github.com/lucacastelnuovo)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
