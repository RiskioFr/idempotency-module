<?php

namespace spec\Riskio\IdempotencyModule;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PhpSpec\ObjectBehavior;
use Riskio\IdempotencyModule\Exception\ExceptionInterface;
use Riskio\IdempotencyModule\Exception\IdempotencyKeyNotFoundException;
use Riskio\IdempotencyModule\Exception\InvalidRequestChecksumException;
use Riskio\IdempotencyModule\Exception\NoIdempotencyKeyHeaderException;
use Riskio\IdempotencyModule\IdempotencyMiddleware;
use Riskio\IdempotencyModule\IdempotencyService;
use Zend\Diactoros\ServerRequest as HttpRequest;
use Zend\Diactoros\Response as HttpResponse;

class IdempotencyMiddlewareSpec extends ObjectBehavior
{
    function let(IdempotencyService $idempotencyService)
    {
        $this->beConstructedWith($idempotencyService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotencyMiddleware::class);
    }

    function it_saves_response_when_http_request_does_not_contain_idempotency_key_header(
        IdempotencyService $idempotencyService,
        DelegateInterface $delegate
    ) {
        $httpRequest = new HttpRequest();
        $httpResponse = new HttpResponse();

        $idempotencyService->getResponse($httpRequest)->willThrow(new NoIdempotencyKeyHeaderException());
        $idempotencyService->save($httpRequest, $httpResponse)->willReturn(null);

        $delegate->process($httpRequest)->willReturn($httpResponse);

        $this->process($httpRequest, $delegate)->shouldReturnAnInstanceOf(HttpResponse::class);
    }

    function it_saves_response_when_idempotency_key_has_expired_or_does_not_exists(
        IdempotencyService $idempotencyService,
        DelegateInterface $delegate
    ) {
        $httpRequest = new HttpRequest();
        $httpResponse = new HttpResponse();

        $idempotencyService->getResponse($httpRequest)->willThrow(new IdempotencyKeyNotFoundException());
        $idempotencyService->save($httpRequest, $httpResponse)->willReturn(null);

        $delegate->process($httpRequest)->willReturn($httpResponse);

        $this->process($httpRequest, $delegate)->shouldReturnAnInstanceOf(HttpResponse::class);
    }

    function it_throws_exception_when_idempotency_service_throw_exception_retrieving_http_response(
        IdempotencyService $idempotencyService,
        DelegateInterface $delegate
    ) {
        $httpRequest = new HttpRequest();
        $httpResponse = new HttpResponse();

        $idempotencyService->getResponse($httpRequest)->willThrow(new InvalidRequestChecksumException());
        $idempotencyService->save($httpRequest, $httpResponse)->shouldNotBeCalled();

        $delegate->process($httpRequest)->shouldNotBeCalled();

        $this->shouldThrow(ExceptionInterface::class)->duringProcess($httpRequest, $delegate);
    }
}
