<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class InvalidIdempotentKeyFormatException extends IdempotentRequestException
{
    protected $message = 'Invalid idempotent key format';
}
