<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class NoIdempotentKeyException extends IdempotentRequestException
{
    protected $message = 'No idempotent key';
}
