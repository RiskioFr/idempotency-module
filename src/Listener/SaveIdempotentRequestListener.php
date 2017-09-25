<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule\Listener;

use Riskio\IdempotencyModule\Exception\NoIdempotencyKeyHeaderException;
use Riskio\IdempotencyModule\IdempotencyService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use Zend\Psr7Bridge\Psr7Response;
use Zend\Psr7Bridge\Psr7ServerRequest;

class SaveIdempotentRequestListener extends AbstractListenerAggregate
{
    private $idempotencyService;

    public function __construct(IdempotencyService $idempotencyService)
    {
        $this->idempotencyService = $idempotencyService;
    }

    public function attach(EventManagerInterface $events, $priority = -100)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, $this, $priority);
    }

    public function __invoke(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        try {
            $this->idempotencyService->save(
                Psr7ServerRequest::fromZend($event->getRequest()),
                Psr7Response::fromZend($event->getResponse())
            );
        } catch (NoIdempotencyKeyHeaderException $event) {
            return;
        }
    }
}
