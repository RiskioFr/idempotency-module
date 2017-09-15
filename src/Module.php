<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $request = $e->getRequest();

        if ($request instanceof HttpRequest) {
            $container = $app->getServiceManager();
            $eventManager = $app->getEventManager();

            $container->get(IdempotentRequestListener::class)->attach($eventManager);
        }
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
