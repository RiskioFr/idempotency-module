<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class IdempotencyKeyNotFoundException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'Idempotent key does not exist or has expired';
}
