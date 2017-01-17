<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Adr\Payload;
use Venta\Contracts\Container\Invoker;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteDispatcher as RouteDispatcherContract;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
final class RouteDispatcher implements RouteDispatcherContract
{

    /**
     * @var Invoker
     */
    private $invoker;

    /**
     * @var RouteContract
     */
    private $route;

    /**
     * RouteDispatcher constructor.
     *
     * @param Invoker $invoker
     * @param RouteContract $route
     */
    public function __construct(Invoker $invoker, RouteContract $route)
    {
        $this->invoker = $invoker;
        $this->route = $route;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        // Add current route to the request.
        $request = $request->withAttribute('route', $this->route);

        if ($this->invoker->isCallable($this->route->domain())) {
            if ($this->invoker->isCallable([$this->route->input(), 'process'])) {
                $arguments = $this->invoker->call([$this->route->input(), 'process'], [$request]);
            }
            /** @var Payload $payload */
            $payload = $this->invoker->call($this->route->domain(), $arguments ?? []);
        }
        return $this->invoker->call([$this->route->responder(), 'run'], [$request, $payload ?? null]);
    }

}