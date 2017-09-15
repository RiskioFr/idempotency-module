<?php

namespace spec\Riskio\IdempotencyModule;

use Faker\Factory as FakerFactory;
use PhpSpec\ObjectBehavior;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotentKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Zend\Diactoros\Request as HttpRequest;
use Zend\Validator\ValidatorInterface;

class IdempotentKeyExtractorSpec extends ObjectBehavior
{
    function let(ValidatorInterface $idempotentKeyValidator)
    {
        $this->beConstructedWith($idempotentKeyValidator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotentKeyExtractor::class);
    }

    function it_extracts_idempotent_key_from_request(ValidatorInterface $idempotentKeyValidator)
    {
        $faker = FakerFactory::create();
        $idempotentKey = $faker->uuid;

        $httpRequest = $this->createHttpRequestWithItempotentKey($idempotentKey);

        $idempotentKeyValidator->isValid($idempotentKey)->willReturn(true);

        $this->extract($httpRequest)->shouldReturn($idempotentKey);
    }

    function it_throws_exception_when_extracting_idempotent_key_from_request_without_idempotent_key()
    {
        $httpRequest = $this->createHttpRequestWithoutIdempotentKey();

        $this->shouldThrow(Exception\NoIdempotentKeyException::class)
            ->duringExtract($httpRequest);
    }

    function it_throws_exception_when_extracting_idempotent_key_with_invalid_format_from_request(
        ValidatorInterface $idempotentKeyValidator
    ) {
        $faker = FakerFactory::create();
        $idempotentKey = $faker->uuid;

        $httpRequest = $this->createHttpRequestWithItempotentKey($idempotentKey);

        $idempotentKeyValidator->isValid($idempotentKey)->willReturn(false);

        $this->shouldThrow(Exception\InvalidIdempotentKeyFormatException::class)
            ->duringExtract($httpRequest);
    }

    private function createHttpRequestWithItempotentKey(string $idempotentKey)
    {
        return $this->createHttpRequestWithoutIdempotentKey()->withHeader(
            IdempotentRequestService::IDEMPOTENCY_HEADER,
            $idempotentKey
        );
    }

    private function createHttpRequestWithoutIdempotentKey()
    {
        return (new HttpRequest('/', 'POST'));
    }
}
