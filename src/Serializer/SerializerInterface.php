<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Serializer;

use Riskio\IdempotencyModule\IdempotentRequest;

interface SerializerInterface
{
    public function serialize(IdempotentRequest $idempotentRequest) : string;

    public function unserialize(string $idempotentRequest) : IdempotentRequest;
}
