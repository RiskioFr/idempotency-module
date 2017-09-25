<?php

namespace spec\Riskio\IdempotencyModule\Storage;

use Faker\Factory as FakerFactory;
use PhpSpec\ObjectBehavior;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotentRequest;
use Riskio\IdempotencyModule\Serializer\SerializerInterface;
use Riskio\IdempotencyModule\Storage\Storage;

class StorageSpec extends ObjectBehavior
{
    function let(
        CacheItemPoolInterface $cacheItemPool,
        SerializerInterface $idempotentRequestSerializer
    ) {
        $this->beConstructedWith($cacheItemPool, $idempotentRequestSerializer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Storage::class);
    }

    function it_retrieves_idempotent_request_from_idempotency_key(
        CacheItemPoolInterface $cacheItemPool,
        CacheItemInterface $cacheItem,
        SerializerInterface $idempotentRequestSerializer,
        IdempotentRequest $idempotentRequest
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;

        $serializedIdempotentRequest = $faker->sha1;

        $cacheItemPool->hasItem($idempotencyKey)->willReturn(true);
        $cacheItemPool->getItem($idempotencyKey)->willReturn($cacheItem);

        $cacheItem->get()->willReturn($serializedIdempotentRequest);

        $idempotentRequestSerializer
            ->unserialize($serializedIdempotentRequest)
            ->willReturn($idempotentRequest);

        $this->get($idempotencyKey)->shouldReturnAnInstanceOf(IdempotentRequest::class);
    }

    function it_throws_exception_when_retrieving_idempotent_request_from_non_existing_idempotent_key(
        CacheItemPoolInterface $cacheItemPool
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;

        $cacheItemPool->hasItem($idempotencyKey)
            ->willReturn(false);

        $this->shouldThrow(Exception\IdempotencyKeyNotFoundException::class)
            ->duringGet($idempotencyKey);
    }

    function it_saves_idempotent_request(
        CacheItemPoolInterface $cacheItemPool,
        CacheItemInterface $cacheItem,
        SerializerInterface $idempotentRequestSerializer,
        IdempotentRequest $idempotentRequest
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;

        $serializedIdempotentRequest = $faker->sha1;

        $idempotentRequestSerializer
            ->serialize($idempotentRequest)
            ->willReturn($serializedIdempotentRequest);

        $cacheItemPool->getItem($idempotencyKey)->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->willReturn(null);

        $cacheItem->set($serializedIdempotentRequest);

        $this->save($idempotencyKey, $idempotentRequest);
    }
}
