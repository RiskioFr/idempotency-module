<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Zend\EventManager\EventManager;

final class IdempotentRequestListenerFactory
{
    public function __invoke(ContainerInterface $container) : IdempotentRequestListener
    {
        $eventManager = $container->get(EventManager::class);
        $idempotentRequestService = $container->get(IdempotentRequestService::class);

        return new IdempotentRequestListener($eventManager, $idempotentRequestService);
    }
}
