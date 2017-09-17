<?php

namespace spec\Riskio\IdempotencyModule;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Riskio\IdempotencyModule\Exception\InvalidIdempotencyKeyFormatException;
use Riskio\IdempotencyModule\Exception\InvalidRequestChecksumException;
use Riskio\IdempotencyModule\Exception\RuntimeException;
use Riskio\IdempotencyModule\IdempotentRequestListener;
use Riskio\IdempotencyModule\IdempotencyService;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\EmptyResponse as DiactorosResponse;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ResponseCollection;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class IdempotentRequestListenerSpec extends ObjectBehavior
{
    function let(
        EventManagerInterface $eventManager,
        IdempotencyService $idempotencyService
    ) {
        $this->beConstructedWith($eventManager, $idempotencyService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdempotentRequestListener::class);
    }

    function it_returns_existing_http_response(
        MvcEvent $mvcEvent,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();
        $psr7Response = new DiactorosResponse();

        $mvcEvent->getRequest()->willReturn($request);
        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willReturn($psr7Response);

        $this->loadResponse($mvcEvent)->shouldReturnAnInstanceOf(Response::class);
    }

    function it_returns_null_when_an_idempotency_exception_is_thrown(
        MvcEvent $mvcEvent,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();

        $mvcEvent->getRequest()->willReturn($request);
        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willThrow(new RuntimeException());

        $this->loadResponse($mvcEvent)->shouldReturn(null);
    }

    function it_returns_null_when_an_invalid_request_checksum_exception_is_thrown(
        EventManagerInterface $eventManager,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);

        $responseCollection = new ResponseCollection();

        $eventManager->triggerEvent($mvcEvent)->willReturn($responseCollection);

        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willThrow(new InvalidRequestChecksumException());

        $this->loadResponse($mvcEvent);
    }

    function it_returns_null_when_an_invalid_idempotent_key_format_exception_is_thrown(
        EventManagerInterface $eventManager,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);

        $responseCollection = new ResponseCollection();

        $eventManager->triggerEvent($mvcEvent)->willReturn($responseCollection);

        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willThrow(new InvalidIdempotencyKeyFormatException());

        $this->loadResponse($mvcEvent);
    }

    function it_saves_http_response(
        MvcEvent $mvcEvent,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();
        $response = new Response();

        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->getResponse()->willReturn($response);

        $idempotencyService
            ->save(Argument::type(ServerRequest::class), Argument::type(PsrResponse::class))
            ->shouldBeCalled();

        $this->saveResponse($mvcEvent);
    }
}
