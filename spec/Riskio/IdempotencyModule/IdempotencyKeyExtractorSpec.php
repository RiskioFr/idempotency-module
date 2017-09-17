<?php

namespace spec\Riskio\IdempotencyModule;

use Faker\Factory as FakerFactory;
use PhpSpec\ObjectBehavior;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Zend\Diactoros\Request as HttpRequest;
use Zend\Validator\ValidatorInterface;

class IdempotencyKeyExtractorSpec extends ObjectBehavior
{
    private $idempotencyKeyHeader = 'Idempotency-Key';

    function let(ValidatorInterface $idempotencyKeyValidator)
    {
        $this->beConstructedWith($idempotencyKeyValidator, $this->idempotencyKeyHeader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotencyKeyExtractor::class);
    }

    function it_extracts_idempotent_key_from_request(ValidatorInterface $idempotencyKeyValidator)
    {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;

        $httpRequest = $this->createHttpRequestWithItempotencyKey($idempotencyKey);

        $idempotencyKeyValidator->isValid($idempotencyKey)->willReturn(true);

        $this->extract($httpRequest)->shouldReturn($idempotencyKey);
    }

    function it_throws_exception_when_extracting_idempotent_key_from_request_without_idempotent_key()
    {
        $httpRequest = $this->createHttpRequestWithoutIdempotencyKey();

        $this->shouldThrow(Exception\NoIdempotencyKeyException::class)
            ->duringExtract($httpRequest);
    }

    function it_throws_exception_when_extracting_idempotent_key_with_invalid_format_from_request(
        ValidatorInterface $idempotencyKeyValidator
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;

        $httpRequest = $this->createHttpRequestWithItempotencyKey($idempotencyKey);

        $idempotencyKeyValidator->isValid($idempotencyKey)->willReturn(false);

        $this->shouldThrow(Exception\InvalidIdempotencyKeyFormatException::class)
            ->duringExtract($httpRequest);
    }

    private function createHttpRequestWithItempotencyKey(string $idempotencyKey)
    {
        return $this->createHttpRequestWithoutIdempotencyKey()->withHeader(
            $this->idempotencyKeyHeader,
            $idempotencyKey
        );
    }

    private function createHttpRequestWithoutIdempotencyKey()
    {
        return (new HttpRequest('/', 'POST'));
    }
}
