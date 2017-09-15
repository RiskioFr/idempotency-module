<?php

namespace spec\Riskio\IdempotencyModule;

use Faker\Factory as FakerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Riskio\IdempotencyModule\Exception;
use Riskio\IdempotencyModule\IdempotentKeyExtractor;
use Riskio\IdempotencyModule\IdempotentRequest;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Riskio\IdempotencyModule\Storage\StorageInterface;
use Riskio\IdempotencyModule\RequestChecksumGeneratorInterface;
use Zend\Diactoros\Request as HttpRequest;
use Zend\Diactoros\Response as HttpResponse;

class IdempotentRequestServiceSpec extends ObjectBehavior
{
    function let(
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        StorageInterface $idempotentRequestStorage,
        IdempotentKeyExtractor $idempotentKeyExtractor
    ) {
        $this->beConstructedWith($requestChecksumGenerator, $idempotentRequestStorage, $idempotentKeyExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotentRequestService::class);
    }

    function it_retrieves_http_response_from_request_with_idempotency_key(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotentRequest $idempotentRequest,
        IdempotentKeyExtractor $idempotentKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotentKey = $faker->uuid;
        $checksum = $faker->sha1;

        $httpRequest = $this->createHttpRequestWithItempotentKey($idempotentKey);
        $httpResponse = new HttpResponse();

        $idempotentRequestStorage->get($idempotentKey)->willReturn($idempotentRequest);

        $idempotentRequest->getChecksum()->willReturn($checksum);
        $idempotentRequest->getHttpResponse()->willReturn($httpResponse);

        $requestChecksumGenerator->generate($httpRequest)->willReturn($checksum);

        $idempotentKeyExtractor->extract($httpRequest)->willReturn($idempotentKey);

        $this->getResponse($httpRequest)->shouldReturnAnInstanceOf(HttpResponse::class);
    }

    function it_throws_exception_when_retrieving_http_response_from_not_consistent_http_request_with_idempotent_key(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotentRequest $idempotentRequest,
        IdempotentKeyExtractor $idempotentKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotentKey = $faker->uuid;
        $checksum = $faker->sha1;
        $generatedChecksum = $faker->sha1;

        $idempotentRequest->getChecksum()->willReturn($checksum);

        $httpRequest = $this->createHttpRequestWithItempotentKey($idempotentKey);

        $idempotentRequestStorage->get($idempotentKey)->willReturn($idempotentRequest);
        $requestChecksumGenerator->generate($httpRequest)->willReturn($generatedChecksum);

        $idempotentKeyExtractor->extract($httpRequest)->willReturn($idempotentKey);

        $this->shouldThrow(Exception\InvalidRequestChecksumException::class)
            ->duringGetResponse($httpRequest);
    }

    function it_saves_http_response(
        StorageInterface $idempotentRequestStorage,
        RequestChecksumGeneratorInterface $requestChecksumGenerator,
        IdempotentKeyExtractor $idempotentKeyExtractor
    ) {
        $faker = FakerFactory::create();
        $idempotentKey = $faker->uuid;
        $checksum = $faker->sha1;

        $httpRequest = $this->createHttpRequestWithItempotentKey($idempotentKey);
        $httpResponse = new HttpResponse();

        $idempotentKeyExtractor->extract($httpRequest)->willReturn($idempotentKey);

        $requestChecksumGenerator->generate($httpRequest)->willReturn($checksum);

        $idempotentRequestStorage
            ->save($idempotentKey, Argument::type(IdempotentRequest::class))
            ->shouldBeCalled();

        $this->save($httpRequest, $httpResponse);
    }

    private function createHttpRequestWithItempotentKey(string $idempotentKey)
    {
        return $this->createHttpRequest()->withHeader(
            IdempotentRequestService::IDEMPOTENCY_HEADER,
            $idempotentKey
        );
    }

    private function createHttpRequest()
    {
        return (new HttpRequest('/', 'POST'));
    }
}
