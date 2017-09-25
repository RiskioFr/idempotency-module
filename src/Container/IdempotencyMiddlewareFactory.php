<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\Middleware\IdempotencyMiddleware;
use Riskio\IdempotencyModule\IdempotencyService;

final class IdempotencyMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : IdempotencyMiddleware
    {
        $idempotencyService = $container->get(IdempotencyService::class);

        return new IdempotencyMiddleware($idempotencyService);
    }
}
