<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request\Serializer as HttpRequestSerializer;

class RequestChecksumGenerator implements RequestChecksumGeneratorInterface
{
    public function generate(RequestInterface $request) : string
    {
        return sha1(HttpRequestSerializer::toString($request));
    }
}
