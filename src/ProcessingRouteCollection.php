<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\RouteGroup as RouteGroupContract;
use Venta\Contracts\Routing\RouteProcessor;

/**
 * Class ProcessingRouteCollection
 *
 * @package Venta\Routing
 */
class ProcessingRouteCollection implements RouteCollectionContract
{

    /**
     * @var RouteProcessor
     */
    private $processor;

    /**
     * @var RouteCollectionContract
     */
    private $routes;

    /**
     * ProcessingRouteCollection constructor.
     *
     * @param RouteCollectionContract $routes
     * @param RouteProcessor $processor
     */
    public function __construct(RouteCollectionContract $routes, RouteProcessor $processor)
    {
        $this->routes = $routes;
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     */
    public function addGroup(RouteGroupContract $group): RouteCollectionContract
    {
        $this->routes->addGroup($group);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRoute(RouteContract $route): RouteCollectionContract
    {
        $this->routes->addRoute($this->processor->process($route));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $routeName)
    {
        return $this->routes->findByName($routeName);
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->routes->getRoutes();
    }

}