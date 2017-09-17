<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Storage;

use Riskio\IdempotencyModule\IdempotentRequest;

interface StorageInterface
{
    public function get(string $idempotencyKey) : IdempotentRequest;

    public function save(string $idempotencyKey, IdempotentRequest $idempotentRequest);
}
