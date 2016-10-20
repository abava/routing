<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\MiddlewarePipelineFactory as MiddlewarePipelineFactoryContract;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\RouteDispatcherFactory as RouteDispatcherFactoryContract;
use Venta\Contracts\Routing\RouteMatcher as RouteMatcherContract;

/**
 * Class Router
 *
 * @package Venta\Routing
 */
class Router implements Delegate
{
    /**
     * @var RouteDispatcherFactoryContract
     */
    private $dispatcherFactory;

    /**
     * @var RouteMatcherContract
     */
    private $matcher;

    /**
     * @var MiddlewarePipelineFactoryContract
     */
    private $pipelineFactory;

    /**
     * @var RouteCollectionContract
     */
    private $routes;

    /**
     * Router constructor.
     *
     * @param RouteMatcherContract $matcher
     * @param MiddlewarePipelineFactoryContract $pipelineFactory
     * @param RouteCollectionContract $routes
     * @param RouteDispatcherFactoryContract $dispatcherFactory
     */
    public function __construct(
        RouteMatcherContract $matcher,
        MiddlewarePipelineFactoryContract $pipelineFactory,
        RouteCollectionContract $routes,
        RouteDispatcherFactoryContract $dispatcherFactory
    ) {
        $this->matcher = $matcher;
        $this->pipelineFactory = $pipelineFactory;
        $this->routes = $routes;
        $this->dispatcherFactory = $dispatcherFactory;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        // Find matching route against provided request
        $route = $this->matcher->match($request, $this->routes);

        // Create route middleware pipeline
        $pipeline = $this->pipelineFactory->create($route->getMiddlewares());
        // Create the last delegate, which calls route handler
        $delegate = $this->dispatcherFactory->create($route);

        return $pipeline->process($request, $delegate);
    }


}