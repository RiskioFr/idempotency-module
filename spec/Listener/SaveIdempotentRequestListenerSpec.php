<?php

namespace spec\Riskio\IdempotencyModule\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Riskio\IdempotencyModule\IdempotencyService;
use Riskio\IdempotencyModule\Listener\SaveIdempotentRequestListener;
use Zend\Diactoros\ServerRequest;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class SaveIdempotentRequestListenerSpec extends ObjectBehavior
{
    function let(IdempotencyService $idempotencyService)
    {
        $this->beConstructedWith($idempotencyService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SaveIdempotentRequestListener::class);
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

        $this->__invoke($mvcEvent);
    }
}
