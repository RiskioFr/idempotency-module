<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Container;

use Interop\Container\ContainerInterface;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\RequestChecksumGenerator;
use Riskio\IdempotencyModule\Storage\Storage;

final class IdempotencyServiceFactory
{
    public function __invoke(ContainerInterface $container) : IdempotencyService
    {
        $requestChecksumGenerator = $container->get(RequestChecksumGenerator::class);
        $storage = $container->get(Storage::class);
        $idempotencyKeyExtractor = $container->get(IdempotencyKeyExtractor::class);

        return new IdempotencyService($requestChecksumGenerator, $storage, $idempotencyKeyExtractor);
    }
}
