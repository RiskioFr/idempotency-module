<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\ResponseInterface;

class IdempotentRequest
{
    /**
     * @var string
     */
    private $checksum;

    /**
     * @var ResponseInterface
     */
    private $httpResponse;

    public function __construct(string $checksum, ResponseInterface $httpResponse)
    {
        $this->checksum = $checksum;
        $this->httpResponse = $httpResponse;
    }

    public function getChecksum() : string
    {
        return $this->checksum;
    }

    public function getHttpResponse() : ResponseInterface
    {
        return $this->httpResponse;
    }
}
