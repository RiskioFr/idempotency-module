<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Riskio\IdempotencyModule\RequestChecksumGenerator;
use Riskio\IdempotencyModule\Storage\Storage;

final class IdempotentRequestServiceFactory
{
    public function __invoke(ContainerInterface $container) : IdempotentRequestService
    {
        $requestChecksumGenerator = $container->get(RequestChecksumGenerator::class);
        $storage = $container->get(Storage::class);
        $idempotencyKeyExtractor = $container->get(IdempotencyKeyExtractor::class);

        return new IdempotentRequestService($requestChecksumGenerator, $storage, $idempotencyKeyExtractor);
    }
}
