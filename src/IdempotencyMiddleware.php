<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Riskio\IdempotencyModule\Exception\IdempotencyKeyNotFoundException;
use Riskio\IdempotencyModule\Exception\NoIdempotencyKeyHeaderException;
use Riskio\IdempotencyModule\Exception\NotEligibleHttpMethodException;

class IdempotencyMiddleware implements MiddlewareInterface
{
    private $idempotencyService;

    public function __construct(IdempotencyService $idempotencyService)
    {
        $this->idempotencyService = $idempotencyService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        try {
            return $this->idempotencyService->getResponse($request);
        } catch (IdempotencyKeyNotFoundException $exception) {
            $response = $delegate->process($request);
        } catch (NoIdempotencyKeyHeaderException $exception) {
            $response = $delegate->process($request);
        } catch (NotEligibleHttpMethodException $exception) {
            $response = $delegate->process($request);
        }

        try {
            $this->idempotencyService->save($request, $response);
        } catch (NoIdempotencyKeyHeaderException $event) {
            return $response;
        }

        return $response;
    }
}
