<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class NoIdempotencyKeyException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'No idempotent key';
}
