<?php
use Symfony\Component\Cache\Adapter\NullAdapter as NullCacheAdapter;
use Riskio\IdempotencyModule\Container\IdempotentKeyExtractorFactory;
use Riskio\IdempotencyModule\Container\IdempotentRequestListenerFactory;
use Riskio\IdempotencyModule\Container\IdempotentRequestServiceFactory;
use Riskio\IdempotencyModule\Container\ModuleOptionsFactory;
use Riskio\IdempotencyModule\Container\StorageFactory;
use Riskio\IdempotencyModule\IdempotentKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Riskio\IdempotencyModule\ModuleOptions;
use Riskio\IdempotencyModule\RequestChecksumGenerator;
use Riskio\IdempotencyModule\Serializer\Serializer;
use Riskio\IdempotencyModule\Storage\Storage;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Validator\NotEmpty as NotEmptyValidator;

return [
    'idempotency' => [
        'cache' => NullCacheAdapter::class,
        'serializer' => Serializer::class,
        'idempotent_key_validator' => NotEmptyValidator::class,
    ],

    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            IdempotentRequestListener::class => IdempotentRequestListenerFactory::class,
            IdempotentRequestService::class => IdempotentRequestServiceFactory::class,
            Storage::class => StorageFactory::class,
            Serializer::class => InvokableFactory::class,
            IdempotentKeyExtractor::class => IdempotentKeyExtractorFactory::class,
            RequestChecksumGenerator::class => InvokableFactory::class,
            NullCacheAdapter::class => InvokableFactory::class,
            NotEmptyValidator::class => InvokableFactory::class,
        ],
    ],
];
