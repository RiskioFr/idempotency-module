<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\Listener\SaveIdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotencyService;

final class SaveIdempotentRequestListenerFactory
{
    public function __invoke(ContainerInterface $container) : SaveIdempotentRequestListener
    {
        $idempotencyService = $container->get(IdempotencyService::class);

        return new SaveIdempotentRequestListener($idempotencyService);
    }
}
