# Laravel Payable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pratiksh/payable.svg?style=flat-square)](https://packagist.org/packages/pratiksh/payable)
[![Stars](https://img.shields.io/github/stars/pratiksh404/payable)](https://github.com/pratiksh404/payable/stargazers) [![Downloads](https://img.shields.io/packagist/dt/pratiksh/payable.svg?style=flat-square)](https://packagist.org/packages/pratiksh/payable) [![StyleCI](https://github.styleci.io/repos/372560942/shield?branch=main)](https://github.styleci.io/repos/372560942?branch=main) [![Build Status](https://scrutinizer-ci.com/g/pratiksh404/payable/badges/build.png?b=main)](https://scrutinizer-ci.com/g/pratiksh404/payable/build-status/main) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pratiksh404/payable/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/pratiksh404/payable/?branch=main) [![CodeFactor](https://www.codefactor.io/repository/github/pratiksh404/payable/badge)](https://www.codefactor.io/repository/github/pratiksh404/payable) [![License](https://img.shields.io/github/license/pratiksh404/payable)](//packagist.org/packages/pratiksh/payable)

Storing payment made simple.

For detailed documentation visit [Payable Documentation](https://pratikdai404.gitbook.io/laravel-payable/)

## Installation

You can install the package via composer:

```bash
composer require pratiksh/payable
```

## Publish Migrations
Packages Contains 3 table
 - fiscals
 - payments
 - payment_histories

```sh
php artisan vendor:publish --tag=payable-migrations
```

## Publish Config file

Install payable

```sh
php artisan vendor:publish --tag=payable-config
```

Migrate Database

```sh
php artisan migrate
```


## Setup
Payment is `polymorphic`, hence with the use of trait `HasPayable` can be used with any model.
```
use Pratiksh\Payable\Traits\HasPayable;

class Product extends Model
{
    use  HasPayable;
}
```

## Usages
```
$product = Product::first();
$product->pay(100)
```


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email pratikdai404@gmail.com instead of using the issue tracker.

## Credits

- [Pratik Shrestha](https://github.com/pratiksh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).


