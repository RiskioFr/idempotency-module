<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class NoIdempotentKeyException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'No idempotent key';
}
