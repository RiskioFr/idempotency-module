Idempotency module for Zend Framework
=====================================

Zend Framework module ensuring at most once requests for mutating endpoints.

[![Latest Stable Version](http://img.shields.io/packagist/v/riskio/idempotency-module.svg?style=flat-square)](https://packagist.org/packages/riskio/idempotency-module)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/RiskioFr/idempotency-module.svg?style=flat-square)](http://travis-ci.org/RiskioFr/idempotency-module)
[![Total Downloads](http://img.shields.io/packagist/dt/riskio/idempotency-module.svg?style=flat-square)](https://packagist.org/packages/riskio/idempotency-module)

While the inherently idempotent HTTP semantics around PUT and DELETE are a good fit for many API calls, what if we have an operation that needs to be invoked exactly once and no more? An example might be if we were designing an API endpoint to charge a customer money; accidentally calling it twice would lead to the customer being double-charged, which is very bad.

This is where idempotency keys come into play. When performing a request, a client generates a unique ID to identify just that operation and sends it up to the server along with the normal payload. The server receives the ID and correlates it with the state of the request on its end. If the client notices a failure, it retries the request with the same ID, and from there it’s up to the server to figure out what to do with it.

Stripe describes its solution in a [blog post](https://stripe.com/blog/idempotency) which provided the inspiration for this package.

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
    'riskio_idempotency' => [
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
