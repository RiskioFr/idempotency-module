<?php

namespace spec\Riskio\IdempotencyModule;

use Faker\Factory as FakerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotencyKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequest;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\Storage\StorageInterface;
use Riskio\IdempotencyModule\RequestChecksumGeneratorInterface;
use Zend\Diactoros\Request as HttpRequest;
use Zend\Diactoros\Response as HttpResponse;

class IdempotencyServiceSpec extends ObjectBehavior
{
    function let(
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        StorageInterface $idempotentRequestStorage,
        IdempotencyKeyExtractor $idempotencyKeyExtractor
    ) {
        $this->beConstructedWith($requestChecksumGenerator, $idempotentRequestStorage, $idempotencyKeyExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotencyService::class);
    }

    function it_retrieves_http_response_from_request_with_idempotency_key(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotentRequest $idempotentRequest,
        IdempotencyKeyExtractor $idempotencyKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;
        $checksum = $faker->sha1;

        $httpRequest = $this->createHttpRequestWithItempotencyKey($idempotencyKey);
        $httpResponse = new HttpResponse();

        $idempotentRequestStorage->get($idempotencyKey)->willReturn($idempotentRequest);

        $idempotentRequest->getChecksum()->willReturn($checksum);
        $idempotentRequest->getHttpResponse()->willReturn($httpResponse);

        $requestChecksumGenerator->generate($httpRequest)->willReturn($checksum);

        $idempotencyKeyExtractor->extract($httpRequest)->willReturn($idempotencyKey);

        $this->getResponse($httpRequest)->shouldReturnAnInstanceOf(HttpResponse::class);
    }

    function it_throws_exception_when_retrieving_http_response_from_not_consistent_http_request_with_idempotent_key(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotentRequest $idempotentRequest,
        IdempotencyKeyExtractor $idempotencyKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;
        $checksum = $faker->sha1;
        $generatedChecksum = $faker->sha1;

        $idempotentRequest->getChecksum()->willReturn($checksum);

        $httpRequest = $this->createHttpRequestWithItempotencyKey($idempotencyKey);

        $idempotentRequestStorage->get($idempotencyKey)->willReturn($idempotentRequest);
        $requestChecksumGenerator->generate($httpRequest)->willReturn($generatedChecksum);

        $idempotencyKeyExtractor->extract($httpRequest)->willReturn($idempotencyKey);

        $this->shouldThrow(Exception\InvalidRequestChecksumException::class)
            ->duringGetResponse($httpRequest);
    }

    function it_saves_http_response(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotencyKeyExtractor $idempotencyKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotencyKey = $faker->uuid;
        $checksum = $faker->sha1;

        $httpRequest = $this->createHttpRequestWithItempotencyKey($idempotencyKey);
        $httpResponse = new HttpResponse();

        $idempotencyKeyExtractor->extract($httpRequest)->willReturn($idempotencyKey);

        $requestChecksumGenerator->generate($httpRequest)->willReturn($checksum);

        $idempotentRequestStorage
            ->save($idempotencyKey, Argument::type(IdempotentRequest::class))
            ->shouldBeCalled();

        $this->save($httpRequest, $httpResponse);
    }

    private function createHttpRequestWithItempotencyKey(string $idempotencyKey)
    {
        return $this->createHttpRequest()->withHeader(
            IdempotencyService::IDEMPOTENCY_HEADER,
            $idempotencyKey
        );
    }

    private function createHttpRequest()
    {
        return (new HttpRequest('/', 'POST'));
    }
}