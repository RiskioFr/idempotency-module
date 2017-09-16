<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class InvalidIdempotentKeyFormatException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'Invalid idempotent key format';
}
