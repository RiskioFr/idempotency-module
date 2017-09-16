<?php

namespace spec\Riskio\IdempotencyModule\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Riskio\IdempotencyModule\Exception\InvalidIdempotencyKeyFormatException;
use Riskio\IdempotencyModule\Exception\InvalidRequestChecksumException;
use Riskio\IdempotencyModule\Exception\RuntimeException;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\Listener\RequestAlreadyPerfomedListener;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\EmptyResponse as DiactorosResponse;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ResponseCollection;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\MvcEvent;

class RequestAlreadyPerfomedListenerSpec extends ObjectBehavior
{
    function let(IdempotencyService $idempotencyService)
    {
        $this->beConstructedWith($idempotencyService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestAlreadyPerfomedListener::class);
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

        $this->__invoke($mvcEvent)->shouldReturnAnInstanceOf(Response::class);
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

        $this->__invoke($mvcEvent)->shouldReturn(null);
    }

    function it_returns_null_when_an_invalid_request_checksum_exception_is_thrown(
        ApplicationInterface $application,
        EventManagerInterface $eventManager,
        MvcEvent $mvcEvent,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();
        $result = 'foo';

        $application->getEventManager()->willReturn($eventManager);
        $eventManager->triggerEvent($mvcEvent)->willReturn(new ResponseCollection());

        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->setError('invalid_request_checksum')->willReturn(null);
        $mvcEvent->setParam('exception', Argument::type(InvalidRequestChecksumException::class))->willReturn(null);
        $mvcEvent->setName(MvcEvent::EVENT_DISPATCH_ERROR)->willReturn(null);
        $mvcEvent->getApplication()->willReturn($application);
        $mvcEvent->getResult()->willReturn($result);

        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willThrow(new InvalidRequestChecksumException());

        $this->__invoke($mvcEvent);
    }

    function it_returns_null_when_an_invalid_idempotent_key_format_exception_is_thrown(
        ApplicationInterface $application,
        EventManagerInterface $eventManager,
        MvcEvent $mvcEvent,
        IdempotencyService $idempotencyService
    ) {
        $request = new Request();
        $result = 'foo';

        $application->getEventManager()->willReturn($eventManager);
        $eventManager->triggerEvent($mvcEvent)->willReturn(new ResponseCollection());

        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->setError('invalid_idempotent_key_format')->willReturn(null);
        $mvcEvent->setParam('exception', Argument::type(InvalidIdempotencyKeyFormatException::class))->willReturn(null);
        $mvcEvent->setName(MvcEvent::EVENT_DISPATCH_ERROR)->willReturn(null);
        $mvcEvent->getApplication()->willReturn($application);
        $mvcEvent->getResult()->willReturn($result);

        $idempotencyService
            ->getResponse(Argument::type(ServerRequest::class))
            ->willThrow(new InvalidIdempotencyKeyFormatException());

        $this->__invoke($mvcEvent);
    }
}
