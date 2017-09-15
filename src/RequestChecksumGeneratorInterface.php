<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Psr\Http\Message\RequestInterface;

interface RequestChecksumGeneratorInterface
{
    public function generate(RequestInterface $request) : string;
}
