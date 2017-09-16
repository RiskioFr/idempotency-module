<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Riskio\IdempotencyModule\Exception\ExceptionInterface;
use Riskio\IdempotencyModule\Exception\InvalidIdempotentKeyFormatException;
use Riskio\IdempotencyModule\Exception\InvalidRequestChecksumException;
use Riskio\IdempotencyModule\Exception\NoIdempotentKeyException;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use Zend\Psr7Bridge\Psr7Response;
use Zend\Psr7Bridge\Psr7ServerRequest;

class IdempotentRequestListener extends AbstractListenerAggregate
{
    private $eventManager;

    private $idempotentRequestService;

    public function __construct(
        EventManagerInterface $eventManager,
        IdempotentRequestService $idempotentRequestService
    ) {
        $this->eventManager = $eventManager;
        $this->idempotentRequestService = $idempotentRequestService;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'loadResponse'],
            -50
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            [$this, 'saveResponse'],
            -100
        );
    }

    public function loadResponse(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        try {
            $psrResponse = $this->idempotentRequestService->getResponse(
                Psr7ServerRequest::fromZend($request)
            );
        } catch (InvalidRequestChecksumException $exception) {
            $event->setError('invalid_request_checksum');
            $event->setParam('exception', $exception);

            return $this->triggerDispatchErrorEvent($event);
        } catch (InvalidIdempotentKeyFormatException $exception) {
            $event->setError('invalid_idempotent_key_format');
            $event->setParam('exception', $exception);

            return $this->triggerDispatchErrorEvent($event);
        } catch (ExceptionInterface $exception) {
            return;
        }

        return Psr7Response::toZend($psrResponse);
    }

    public function saveResponse(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        try {
            $this->idempotentRequestService->save(
                Psr7ServerRequest::fromZend($event->getRequest()),
                Psr7Response::fromZend($event->getResponse())
            );
        } catch (NoIdempotentKeyException $event) {
            return;
        }
    }

    private function triggerDispatchErrorEvent(MvcEvent $event)
    {
        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);

        $results = $this->eventManager->triggerEvent($event);

        $return = $results->last();
        if (!$return) {
            $return = $event->getResult();
        }

        return $return;
    }
}
