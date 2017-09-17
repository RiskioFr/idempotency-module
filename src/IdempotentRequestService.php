<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Riskio\IdempotencyModule\Storage\StorageInterface;

class IdempotentRequestService
{
    const IDEMPOTENCY_HEADER = 'Idempotency-Key';

    private $requestChecksumGenerator;
    private $idempotentRequestStorage;
    private $idempotencyKeyExtractor;

    public function __construct(
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        StorageInterface $idempotentRequestStorage,
        IdempotencyKeyExtractor $idempotencyKeyExtractor
    ) {
        $this->requestChecksumGenerator = $requestChecksumGenerator;
        $this->idempotentRequestStorage = $idempotentRequestStorage;
        $this->idempotencyKeyExtractor = $idempotencyKeyExtractor;
    }

    public function getResponse(RequestInterface $request) : ResponseInterface
    {
        $idempotencyKey = $this->idempotencyKeyExtractor->extract($request);
        $idempotentRequest = $this->idempotentRequestStorage->get($idempotencyKey);

        $checksum = $this->requestChecksumGenerator->generate($request);

        if ($checksum != $idempotentRequest->getChecksum()) {
            throw new Exception\InvalidRequestChecksumException();
        }

        return $idempotentRequest->getHttpResponse();
    }

    public function save(RequestInterface $request, ResponseInterface $response)
    {
        $idempotencyKey = $this->idempotencyKeyExtractor->extract($request);

        $checksum = $this->requestChecksumGenerator->generate($request);
        $idempotentRequest = new IdempotentRequest($checksum, $response);

        $this->idempotentRequestStorage->save($idempotencyKey, $idempotentRequest);
    }
}
