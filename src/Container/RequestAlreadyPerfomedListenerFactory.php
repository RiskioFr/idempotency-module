<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\Listener\RequestAlreadyPerfomedListener;
use Riskio\IdempotencyModule\IdempotencyService;

final class RequestAlreadyPerfomedListenerFactory
{
    public function __invoke(ContainerInterface $container) : RequestAlreadyPerfomedListener
    {
        $idempotencyService = $container->get(IdempotencyService::class);

        return new RequestAlreadyPerfomedListener($idempotencyService);
    }
}
