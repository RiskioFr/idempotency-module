<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\ModuleOptions;

final class IdempotencyKeyExtractorFactory
{
    public function __invoke(ContainerInterface $container) : IdempotencyKeyExtractor
    {
        /* @var $moduleOptions ModuleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $idempotencyKeyValidator = $container->get($moduleOptions->getIdempotencyKeyValidator());

        return new IdempotencyKeyExtractor($idempotencyKeyValidator);
    }
}
