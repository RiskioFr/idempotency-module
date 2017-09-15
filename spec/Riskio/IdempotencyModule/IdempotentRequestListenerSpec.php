<?php

namespace spec\Riskio\IdempotencyModule;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotentRequestService;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\EmptyResponse as DiactorosResponse;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class IdempotentRequestListenerSpec extends ObjectBehavior
{
    function let(IdempotentRequestService $idempotentRequestService)
    {
        $this->beConstructedWith($idempotentRequestService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotentRequestListener::class);
    }

    function it_returns_existing_http_response(
        MvcEvent $mvcEvent,
        IdempotentRequestService $idempotentRequestService
    ) {
        $request = new Request();
        $psr7Response = new DiactorosResponse();

        $mvcEvent->getRequest()->willReturn($request);
        $idempotentRequestService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willReturn($psr7Response);

        $this->loadResponse($mvcEvent)->shouldReturnAnInstanceOf(Response::class);
    }

    function it_saves_http_response(
        MvcEvent $mvcEvent,
        IdempotentRequestService $idempotentRequestService
    ) {
        $request = new Request();
        $response = new Response();

        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->getResponse()->willReturn($response);

        $idempotentRequestService
            ->save(Argument::type(ServerRequest::class), Argument::type(PsrResponse::class))
            ->shouldBeCalled();

        $this->saveResponse($mvcEvent);
    }
}
