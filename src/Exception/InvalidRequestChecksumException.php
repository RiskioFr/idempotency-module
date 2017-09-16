<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class InvalidRequestChecksumException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'The request parameters do not match idempotent key';
}
