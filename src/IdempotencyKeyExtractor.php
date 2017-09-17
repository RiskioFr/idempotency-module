<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Zend\Validator\ValidatorInterface;

class IdempotencyKeyExtractor
{
    private $idempotencyKeyValidator;

    public function __construct(ValidatorInterface $idempotencyKeyValidator)
    {
        $this->idempotencyKeyValidator = $idempotencyKeyValidator;
    }

    public function extract(RequestInterface $request) : string
    {
        if (!$request->hasHeader(IdempotencyService::IDEMPOTENCY_HEADER)) {
            throw new Exception\NoIdempotencyKeyException();
        }

        $idempotencyKey = $request->getHeader(IdempotencyService::IDEMPOTENCY_HEADER)[0];

        if (!$this->idempotencyKeyValidator->isValid($idempotencyKey)) {
            throw new Exception\InvalidIdempotencyKeyFormatException();
        }

        return $idempotencyKey;
    }
}
