<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class InvalidIdempotencyKeyFormatException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'Invalid idempotent key format';
}
