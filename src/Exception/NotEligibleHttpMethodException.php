<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class NotEligibleHttpMethodException extends RuntimeException implements ExceptionInterface
{
    protected $message = 'Not eligible HTTP method';
}
