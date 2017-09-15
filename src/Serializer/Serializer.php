<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Serializer;

use Riskio\IdempotencyModule\IdempotentRequest;
use Zend\Diactoros\Response\Serializer as HttpResponseSerializer;

class Serializer implements SerializerInterface
{
    public function serialize(IdempotentRequest $idempotentRequest) : string
    {
        return json_encode([
            'checksum' => $idempotentRequest->getChecksum(),
            'response' => HttpResponseSerializer::toString(
                $idempotentRequest->getHttpResponse()
            ),
        ]);
    }

    public function unserialize(string $idempotentRequest) : IdempotentRequest
    {
        $decoded = (array) json_decode($idempotentRequest);

        return new IdempotentRequest(
            $decoded['checksum'],
            HttpResponseSerializer::fromString($decoded['response'])
        );
    }
}
