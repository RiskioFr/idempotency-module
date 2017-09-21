<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Riskio\IdempotencyModule\Exception\NotEligibleHttpMethodException;
use Riskio\IdempotencyModule\Storage\StorageInterface;

class IdempotencyService
{
    private $requestChecksumGenerator;
    private $idempotentRequestStorage;
    private $idempotencyKeyExtractor;
    private $httpMethods;

    public function __construct(
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        StorageInterface $idempotentRequestStorage,
        IdempotencyKeyExtractor $idempotencyKeyExtractor,
        array $httpMethods = []
    ) {
        $this->requestChecksumGenerator = $requestChecksumGenerator;
        $this->idempotentRequestStorage = $idempotentRequestStorage;
        $this->idempotencyKeyExtractor = $idempotencyKeyExtractor;
        $this->httpMethods = $httpMethods;
    }

    public function getResponse(RequestInterface $request) : ResponseInterface
    {
        if (!$this->hasEligibleHttpMethod($request)) {
            throw new NotEligibleHttpMethodException();
        }

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
        if (!$this->hasEligibleHttpMethod($request)) {
            return;
        }

        $idempotencyKey = $this->idempotencyKeyExtractor->extract($request);

        $checksum = $this->requestChecksumGenerator->generate($request);
        $idempotentRequest = new IdempotentRequest($checksum, $response);

        $this->idempotentRequestStorage->save($idempotencyKey, $idempotentRequest);
    }

    private function hasEligibleHttpMethod(RequestInterface $request) : bool
    {
        return in_array($request->getMethod(), $this->httpMethods);
    }
}
