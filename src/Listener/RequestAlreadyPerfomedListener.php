<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Listener;

use Riskio\IdempotencyModule\Exception\ExceptionInterface;
use Riskio\IdempotencyModule\Exception\InvalidIdempotencyKeyFormatException;
use Riskio\IdempotencyModule\Exception\InvalidRequestChecksumException;
use Riskio\IdempotencyModule\IdempotencyService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use Zend\Psr7Bridge\Psr7Response;
use Zend\Psr7Bridge\Psr7ServerRequest;

class RequestAlreadyPerfomedListener extends AbstractListenerAggregate
{
    private $idempotencyService;

    public function __construct(IdempotencyService $idempotencyService)
    {
        $this->idempotencyService = $idempotencyService;
    }

    public function attach(EventManagerInterface $events, $priority = -50)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, $this, $priority);
    }

    public function __invoke(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        try {
            $psrResponse = $this->idempotencyService->getResponse(
                Psr7ServerRequest::fromZend($request)
            );
        } catch (InvalidRequestChecksumException $exception) {
            $event->setError('invalid_request_checksum');
            $event->setParam('exception', $exception);

            return $this->triggerDispatchErrorEvent($event);
        } catch (InvalidIdempotencyKeyFormatException $exception) {
            $event->setError('invalid_idempotent_key_format');
            $event->setParam('exception', $exception);

            return $this->triggerDispatchErrorEvent($event);
        } catch (ExceptionInterface $exception) {
            return;
        }

        return Psr7Response::toZend($psrResponse);
    }

    private function triggerDispatchErrorEvent(MvcEvent $event)
    {
        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);

        $eventManager = $event->getApplication()->getEventManager();
        $results = $eventManager->triggerEvent($event);

        $return = $results->last();
        if (!$return) {
            $return = $event->getResult();
        }

        return $return;
    }
}
