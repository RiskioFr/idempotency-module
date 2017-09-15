<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Zend\Validator\ValidatorInterface;

class IdempotentKeyExtractor
{
    private $idempotentKeyValidator;

    public function __construct(ValidatorInterface $idempotentKeyValidator)
    {
        $this->idempotentKeyValidator = $idempotentKeyValidator;
    }

    public function extract(RequestInterface $request) : string
    {
        if (!$request->hasHeader(IdempotentRequestService::IDEMPOTENCY_HEADER)) {
            throw new Exception\NoIdempotentKeyException();
        }

        $idempotencyKey = $request->getHeader(IdempotentRequestService::IDEMPOTENCY_HEADER)[0];

        if (!$this->idempotentKeyValidator->isValid($idempotencyKey)) {
            throw new Exception\InvalidIdempotentKeyFormatException();
        }

        return $idempotencyKey;
    }
}
