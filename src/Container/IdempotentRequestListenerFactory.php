<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotencyService;

final class IdempotentRequestListenerFactory
{
    public function __invoke(ContainerInterface $container) : IdempotentRequestListener
    {
        $eventManager = $container->get('EventManager');
        $idempotencyService = $container->get(IdempotencyService::class);

        return new IdempotentRequestListener($eventManager, $idempotencyService);
    }
}
