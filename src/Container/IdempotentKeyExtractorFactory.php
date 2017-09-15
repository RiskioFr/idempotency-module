<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotentKeyExtractor;
use Riskio\IdempotencyModule\ModuleOptions;

final class IdempotentKeyExtractorFactory
{
    public function __invoke(ContainerInterface $container) : IdempotentKeyExtractor
    {
        /* @var $moduleOptions ModuleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $idempotentKeyValidator = $container->get($moduleOptions->getIdempotentKeyValidator());

        return new IdempotentKeyExtractor($idempotentKeyValidator);
    }
}
