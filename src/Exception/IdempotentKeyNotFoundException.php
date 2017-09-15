<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class IdempotentKeyNotFoundException extends IdempotentRequestException
{
    protected $message = 'Idempotent key does not exist or has expired';
}
