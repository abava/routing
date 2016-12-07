<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\ImmutableRouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\MiddlewarePipelineFactory as MiddlewarePipelineFactoryContract;
use Venta\Contracts\Routing\RequestRouteCollectionFactory;
use Venta\Contracts\Routing\RouteDispatcherFactory as RouteDispatcherFactoryContract;
use Venta\Contracts\Routing\RouteMatcher as RouteMatcherContract;
use Venta\Contracts\Routing\Router as RouterContract;

/**
 * Class Router
 *
 * @package Venta\Routing
 */
final class Router implements RouterContract
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
     * @var RequestRouteCollectionFactory
     */
    private $routeCollectionFactory;

    /**
     * @var RouteCollectionContract
     */
    private $routes;

    /**
     * Router constructor.
     *
     * @param RouteDispatcherFactoryContract $dispatcherFactory
     * @param RouteMatcherContract $matcher
     * @param MiddlewarePipelineFactoryContract $pipelineFactory
     * @param RouteCollectionContract $routes
     * @param RequestRouteCollectionFactory $routeCollectionFactory
     */
    public function __construct(
        RouteDispatcherFactoryContract $dispatcherFactory,
        RouteMatcherContract $matcher,
        MiddlewarePipelineFactoryContract $pipelineFactory,
        RouteCollectionContract $routes,
        RequestRouteCollectionFactory $routeCollectionFactory
    ) {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->matcher = $matcher;
        $this->pipelineFactory = $pipelineFactory;
        $this->routes = $routes;
        $this->routeCollectionFactory = $routeCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        $requestRouteCollection = $this->routeCollectionFactory->create($this->routes, $request);
        // Find matching route against provided request.
        $route = $this->matcher->match($request, $requestRouteCollection);

        // Create route middleware pipeline.
        $pipeline = $this->pipelineFactory->create($route->getMiddlewares());
        // Create the last delegate, which calls route handler.
        $routeDispatcher = $this->dispatcherFactory->create($route);

        return $pipeline->process($request, $routeDispatcher);
    }

}