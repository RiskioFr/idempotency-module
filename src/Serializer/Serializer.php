<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Serializer;

use Riskio\IdempotencyModule\Exception\SerializationFailedException;
use Riskio\IdempotencyModule\Exception\UnserializationFailedException;
use Riskio\IdempotencyModule\IdempotentRequest;
use Zend\Diactoros\Response\Serializer as HttpResponseSerializer;

class Serializer implements SerializerInterface
{
    private $messages = [
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
    ];

    public function serialize(IdempotentRequest $idempotentRequest) : string
    {
        $result = \json_encode([
            'checksum' => $idempotentRequest->getChecksum(),
            'response' => HttpResponseSerializer::toString(
                $idempotentRequest->getHttpResponse()
            ),
        ]);

        $lastJsonError = \json_last_error();

        if ($lastJsonError !== JSON_ERROR_NONE) {
            throw SerializationFailedException::createFromErrorMessage(
                $this->messages[$lastJsonError]
            );
        }

        return $result;
    }

    public function unserialize(string $idempotentRequest) : IdempotentRequest
    {
        $result = \json_decode($idempotentRequest, true);

        $lastJsonError = \json_last_error();

        if ($lastJsonError !== JSON_ERROR_NONE) {
            throw UnserializationFailedException::createFromErrorMessage(
                $this->messages[$lastJsonError]
            );
        }

        return new IdempotentRequest(
            $result['checksum'],
            HttpResponseSerializer::fromString($result['response'])
        );
    }
}
