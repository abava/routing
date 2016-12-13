<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Adr\Input;
use Venta\Contracts\Adr\Payload;
use Venta\Contracts\Adr\Responder;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Http\Request as RequestContract;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteDispatcher as RouteDispatcherContract;
use Venta\Http\Request;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
final class RouteDispatcher implements RouteDispatcherContract
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteContract
     */
    private $route;

    /**
     * RouteDispatcher constructor.
     *
     * @param RouteContract $route
     * @param Container $container
     */
    public function __construct(RouteContract $route, Container $container)
    {
        $this->route = $route;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        // Add current route to the request.
        $request = $request->withAttribute('route', $this->route);

        if ($this->container->isCallable($this->route->domain())) {
            if ($this->container->has($this->route->input())) {
                /** @var Input $input */
                $input = $this->container->get($this->route->input());
                $arguments = $input->process($request);
            }
            /** @var Payload $payload */
            $payload = $this->container->call($this->route->domain(), $arguments ?? []);
        }
        /** @var Responder $responder */
        $responder = $this->container->get($this->route->responder());
        
        return $responder->run($request, $payload ?? null);
    }

}