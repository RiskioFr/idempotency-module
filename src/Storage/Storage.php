<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Storage;

use Psr\Cache\CacheItemPoolInterface;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotentRequest;
use Riskio\IdempotencyModule\Serializer\SerializerInterface;

class Storage implements StorageInterface
{
    private $cache;
    private $idempotentRequestSerializer;

    public function __construct(
        CacheItemPoolInterface $cache,
        SerializerInterface $idempotentRequestSerializer
    ) {
        $this->cache = $cache;
        $this->idempotentRequestSerializer = $idempotentRequestSerializer;
    }

    public function get(string $idempotentKey) : IdempotentRequest
    {
        if (!$this->cache->hasItem($idempotentKey)) {
            throw new Exception\IdempotentKeyNotFoundException();
        }

        $item = $this->cache->getItem($idempotentKey);

        return $this->idempotentRequestSerializer->unserialize($item->get());
    }

    public function save(string $idempotentKey, IdempotentRequest $idempotentRequest)
    {
        $serializedIdempotentRequest = $this->idempotentRequestSerializer->serialize($idempotentRequest);

        $item = $this->cache->getItem($idempotentKey);
        $item->set($serializedIdempotentRequest);

        $this->cache->save($item);
    }
}
