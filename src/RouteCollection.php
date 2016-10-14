<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\RouteGroup;

/**
 * Class RouteCollection
 *
 * @package Venta\Routing
 */
class RouteCollection implements RouteCollectionContract
{

    /**
     * @var Route[]
     */
    protected $routes = [];

    /**
     * @inheritDoc
     */
    public function addGroup(RouteGroup $group): RouteCollectionContract
    {
        foreach ($group->getRoutes() as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRoute(Route $route): RouteCollectionContract
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}