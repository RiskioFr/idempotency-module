<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class NoIdempotencyKeyHeaderException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'No idempotency key HTTP header';
}
