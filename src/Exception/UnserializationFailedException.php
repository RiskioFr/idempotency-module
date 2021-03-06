<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Exception;

class UnserializationFailedException extends RuntimeException implements ExceptionInterface
{
    private static $message = 'Unserialization failed';

    public static function createFromErrorMessage(string $errorMessage) : self
    {
        return new self(sprintf('%s: %s', self::$message, $errorMessage));
    }
}
