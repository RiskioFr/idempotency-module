<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    private $cache;

    private $serializer;

    private $idempotencyKeyValidator;

    public function getCache() : string
    {
        return $this->cache;
    }

    public function setCache(string $cache)
    {
        $this->cache = $cache;
    }

    public function getSerializer() : string
    {
        return $this->serializer;
    }

    public function setSerializer(string $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getIdempotencyKeyValidator() : string
    {
        return $this->idempotencyKeyValidator;
    }

    public function setIdempotencyKeyValidator(string $idempotencyKeyValidator)
    {
        $this->idempotencyKeyValidator = $idempotencyKeyValidator;
    }
}
