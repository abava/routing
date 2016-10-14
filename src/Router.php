<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Delegate;

/**
 * Class Router
 *
 * @package Venta\Routing
 */
class Router implements Delegate
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RouteMatcher
     */
    protected $matcher;

    public function __construct(RouteMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        // Find matching route against provided request
        $route = $this->matcher->match($request);

        // Create route middleware pipeline
        $routeMiddlewarePipeline = new DeferredMiddlewarePipeline($route->getMiddlewares);

        // Create the last delegate, which calls route handler
        $delegate = new RouteDispatcher($route);
        $delegate->setContainer($this->container);

        return $routeMiddlewarePipeline->process($request, $delegate);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

}