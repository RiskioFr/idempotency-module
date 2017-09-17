Idempotency module for Zend Framework
=====================================

Zend Framework module ensuring at most once requests for mutating endpoints.

[![Latest Stable Version](http://img.shields.io/packagist/v/riskio/idempotency-module.svg?style=flat-square)](https://packagist.org/packages/riskio/idempotency-module)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/RiskioFr/idempotency-module.svg?style=flat-square)](http://travis-ci.org/RiskioFr/idempotency-module)
[![Total Downloads](http://img.shields.io/packagist/dt/riskio/idempotency-module.svg?style=flat-square)](https://packagist.org/packages/riskio/idempotency-module)

While the inherently idempotent HTTP semantics around PUT and DELETE are a good fit for many API calls, what if we have an operation that needs to be invoked exactly once and no more? An example might be if we were designing an API endpoint to charge a customer money; accidentally calling it twice would lead to the customer being double-charged, which is very bad.

This is where *idempotency keys* come into play. When performing a request, a client generates a unique ID to identify just that operation and sends it up to the server along with the normal payload. The server receives the ID and correlates it with the state of the request on its end. If the client notices a failure, it retries the request with the same ID, and from there it’s up to the server to figure out what to do with it.

Stripe describes its solution in a [blog post](https://stripe.com/blog/idempotency) which provided the inspiration for this package.

## Requirements

* PHP 7.0+
* [symfony/cache ^3.0](https://github.com/symfony/cache)
* [zendframework/zend-diactoros ^1.1](https://github.com/zendframework/zend-diactoros)
* [zendframework/zend-eventmanager ^2.6.3 || ^3.0](https://github.com/zendframework/zend-eventmanager)
* [zendframework/zend-http ^2.6](https://github.com/zendframework/zend-http)
* [zendframework/zend-mvc ^3.0](https://github.com/zendframework/zend-mvc)
* [zendframework/zend-psr7bridge ^1.0](https://github.com/zendframework/zend-psr7bridge)
* [zendframework/zend-stdlib ^2.7.3 || ^3.0](https://github.com/zendframework/zend-stdlib)
* [zendframework/zend-validator ^2.0](https://github.com/zendframework/zend-validator)

## Installation

Idempotency module only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

You can install the module from command line:

```sh
$ composer require riskio/idempotency-module
```

After installation of the package, you need to complete the following steps:

 1. Enable the module by adding `Riskio\IdempotencyModule` in your `application.config.php` file.
 2. Copy the `riskio_idempotency.global.php.dist` (you can find this file in the `config` folder of the module) into
your `config/autoload` folder and apply any setting you want.

## Documentation

The module goal is to guarantee the safety of operations on mutating endpoints by allowing clients to pass a unique value with the special **Idempotency-Key** header. 

The clients are in charge of creating the unique keys. The module always send back the same response for requests made with the same key, and keys can't be reused with different request parameters.

### Idempotency key format validation

By default, the module uses V4 UUIDs but you can change validation rules using the config file seen above:
 
```php
<?php
use Zend\Validator\Uuid as UuidValidator;

return [
    'riskio_idempotency' => [
        'idempotency_key_validator' => UuidValidator::class,
    ],
];
```
### Storage of already performed requests

In order to keep track of requests performed with **Idempotency-Key** header, you have to specify a PSR-6 cache compliant service. Keys will expire after a delay related to the lifetime of your cache.
 
```php
<?php
use Symfony\Component\Cache\Adapter\NullAdapter as NullCacheAdapter;

return [
    'riskio_idempotency' => [
        'cache' => NullCacheAdapter::class,
    ],
];
```

> For instance, Stripe has defined that the keys expire after 24 hours.

### Idempotent requests serialization/deserialization

For each idempotent request, the module creates an instance of ```Riskio\IdempotencyModule\IdempotentRequest``` that contains a checksum of the HTTP request performed and the related HTTP response in order to be stored and eventually retrieved in the future if the request is made more than once.

Accordindly, we must provide a serializer that will serialize and unserialize this class. By defaut, the module uses the ```Riskio\IdempotencyModule\Serializer\Serializer``` class but you can provide another one if you want.

```php
<?php
use Riskio\IdempotencyModule\Serializer\Serializer;

return [
    'riskio_idempotency' => [
        'serializer' => Serializer::class,
    ],
];
```

### Eligible HTTP methods

RFC7231 (substituting RFC2616) added two notions to HTTP methods:

* A safe HTTP method is a method that do not modify resources. For instance, using **GET** or **HEAD** on a resource URL, should NEVER change the resource. However, this is not completely true. It means: it won't change the resource representation. It is still possible, that safe methods do change things on a server or resource, but this should not reflect in a different representation.
* An idempotent HTTP method is a method that can be called many times without different outcomes. It would not matter if the method is called only once, or ten times over. The result should be the same. For subsequent calls, the answer will be the same all the time.

Method | Safe | Idempotent
------------ | ------------- | -------------
GET | yes | yes
HEAD | yes | yes
POST | no | no
PUT | no | yes
PATCH | no | no
DELETE | no | yes

Accordingly to the RFC, only **POST** and **PATCH** HTTP methods are not idempotent. Consequently, the module allows clients to use **Idempotent-Key** header only for these methods by default. However, you can configure another HTTP method list to meet your needs as below:

```php
<?php
return [
    'riskio_idempotency' => [
        'http_methods' => ['PATCH', 'POST'],
    ],
];
```

### Idempotency key header customization

By default, the module uses **Idempotency-Key** header to submit unique tokens that guarantee idempotency of endpoints. If you want to use another header name for whatever reason, you can specify it as below:

```php
<?php
return [
    'riskio_idempotency' => [
        'idempotency_key_header' => 'Idempotency-Key',
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
