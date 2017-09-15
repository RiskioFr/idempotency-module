<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\ModuleOptions;
use Riskio\IdempotencyModule\Storage\Storage;

final class StorageFactory
{
    public function __invoke(ContainerInterface $container) : Storage
    {
        /* @var $moduleOptions ModuleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $cacheItemPool = $container->get($moduleOptions->getCache());
        $serializer = $container->get($moduleOptions->getSerializer());

        return new Storage($cacheItemPool, $serializer);
    }
}
