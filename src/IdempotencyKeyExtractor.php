<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Zend\Validator\ValidatorInterface;

class IdempotencyKeyExtractor
{
    private $idempotencyKeyValidator;
    private $idempotencyKeyHeader;

    public function __construct(ValidatorInterface $idempotencyKeyValidator, string $idempotencyKeyHeader)
    {
        $this->idempotencyKeyValidator = $idempotencyKeyValidator;
        $this->idempotencyKeyHeader = $idempotencyKeyHeader;
    }

    public function extract(RequestInterface $request) : string
    {
        if (!$request->hasHeader($this->idempotencyKeyHeader)) {
            throw new Exception\NoIdempotencyKeyHeaderException();
        }

        $idempotencyKey = $request->getHeader($this->idempotencyKeyHeader)[0];

        if (!$this->idempotencyKeyValidator->isValid($idempotencyKey)) {
            throw new Exception\InvalidIdempotencyKeyFormatException();
        }

        return $idempotencyKey;
    }
}
