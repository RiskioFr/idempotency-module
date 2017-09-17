<?php
use Riskio\IdempotencyModule\Container\IdempotencyKeyExtractorFactory;
use Riskio\IdempotencyModule\Container\IdempotentRequestListenerFactory;
use Riskio\IdempotencyModule\Container\IdempotencyServiceFactory;
use Riskio\IdempotencyModule\Container\ModuleOptionsFactory;
use Riskio\IdempotencyModule\Container\StorageFactory;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\ModuleOptions;
use Riskio\IdempotencyModule\RequestChecksumGenerator;
use Riskio\IdempotencyModule\Serializer\Serializer;
use Riskio\IdempotencyModule\Storage\Storage;
use Symfony\Component\Cache\Adapter\NullAdapter as NullCacheAdapter;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Validator\NotEmpty as NotEmptyValidator;

return [
    'riskio_idempotency' => [
        'cache' => NullCacheAdapter::class,
        'serializer' => Serializer::class,
        'idempotency_key_validator' => NotEmptyValidator::class,
    ],

    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            IdempotentRequestListener::class => IdempotentRequestListenerFactory::class,
            IdempotencyService::class => IdempotencyServiceFactory::class,
            Storage::class => StorageFactory::class,
            Serializer::class => InvokableFactory::class,
            IdempotencyKeyExtractor::class => IdempotencyKeyExtractorFactory::class,
            RequestChecksumGenerator::class => InvokableFactory::class,
            NullCacheAdapter::class => InvokableFactory::class,
            NotEmptyValidator::class => InvokableFactory::class,
        ],
    ],
];
