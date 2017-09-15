<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class InvalidRequestChecksumException extends IdempotentRequestException
{
    protected $message = 'The request parameters do not match idempotent key';
}
