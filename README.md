Idempotency module for Zend Framework
=====================================

Zend Framework module ensuring at most once requests for mutating endpoints.

[![Build Status](https://img.shields.io/travis/RiskioFr/idempotency-module.svg?style=flat)](http://travis-ci.org/RiskioFr/idempotency-module)
[![Latest Stable Version](http://img.shields.io/packagist/v/riskio/idempotency-module.svg?style=flat)](https://packagist.org/packages/riskio/idempotency-module)
[![Total Downloads](http://img.shields.io/packagist/dt/riskio/idempotency-module.svg?style=flat)](https://packagist.org/packages/riskio/idempotency-module)

Requirements
------------

* PHP 7.0
* Zend Framework 3

Installation
------------

Idempotency module only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

You can install the module from command line:
```sh
$ php composer.phar require riskio/idempotency-module
```

Enable the module by adding `Riskio\IdempotencyModule` key to your `application.config.php` file.

Default configuration
---------------------

```php
<?php
use Symfony\Component\Cache\Adapter\NullAdapter as NullCacheAdapter;
use Riskio\IdempotencyModule\Serializer\Serializer;
use Zend\Validator\NotEmpty as NotEmptyValidator;

return [
    'idempotency' => [
        'cache' => NullCacheAdapter::class,
        'serializer' => Serializer::class,
        'idempotent_key_validator' => NotEmptyValidator::class,
    ],
];
```
## Testing

``` bash
$ vendor/bin/phpspec run
```

## Credits

- [Nicolas Eeckeloo](https://github.com/neeckeloo)
- [All Contributors](https://github.com/RiskioFr/idempotency-module/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/RiskioFr/idempotency-module/blob/master/LICENSE) for more information.
