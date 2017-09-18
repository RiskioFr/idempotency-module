<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\ModuleOptions;

class ModuleOptionsFactory
{
    public function __invoke(ContainerInterface $container) : ModuleOptions
    {
        $options = $container->get('config');

        return new ModuleOptions($options['riskio_idempotency']);
    }
}
