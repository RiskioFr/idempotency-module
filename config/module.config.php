<?php
use Riskio\IdempotencyModule\Container\IdempotencyKeyExtractorFactory;
use Riskio\IdempotencyModule\Container\IdempotencyServiceFactory;
use Riskio\IdempotencyModule\Container\ModuleOptionsFactory;
use Riskio\IdempotencyModule\Container\RequestAlreadyPerfomedListenerFactory;
use Riskio\IdempotencyModule\Container\SaveIdempotentRequestListenerFactory;
use Riskio\IdempotencyModule\Container\StorageFactory;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\Listener\RequestAlreadyPerfomedListener;
use Riskio\IdempotencyModule\Listener\SaveIdempotentRequestListener;
use Riskio\IdempotencyModule\ModuleOptions;
use Riskio\IdempotencyModule\RequestChecksumGenerator;
use Riskio\IdempotencyModule\Serializer\Serializer;
use Riskio\IdempotencyModule\Storage\Storage;
use Symfony\Component\Cache\Adapter\NullAdapter as NullCacheAdapter;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Validator\Uuid as UuidValidator;

return [
    'riskio_idempotency' => [
        'cache' => NullCacheAdapter::class,
        'serializer' => Serializer::class,
        'idempotency_key_validator' => UuidValidator::class,
        'idempotency_key_header' => 'Idempotency-Key',
        'http_methods' => ['POST', 'PATCH'],
    ],

    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            RequestAlreadyPerfomedListener::class => RequestAlreadyPerfomedListenerFactory::class,
            SaveIdempotentRequestListener::class => SaveIdempotentRequestListenerFactory::class,
            IdempotencyService::class => IdempotencyServiceFactory::class,
            Storage::class => StorageFactory::class,
            Serializer::class => InvokableFactory::class,
            IdempotencyKeyExtractor::class => IdempotencyKeyExtractorFactory::class,
            RequestChecksumGenerator::class => InvokableFactory::class,
            NullCacheAdapter::class => InvokableFactory::class,
            UuidValidator::class => InvokableFactory::class,
        ],
    ],
];
