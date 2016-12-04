<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\MutableRouteCollection;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteGroup as RouteGroupContract;

/**
 * Class RouteCollection
 *
 * @package Venta\Routing
 */
class RouteCollection implements MutableRouteCollection
{

    /**
     * @var RouteContract[]
     */
    protected $routes = [];

    /**
     * @inheritDoc
     */
    public function addGroup(RouteGroupContract $group): MutableRouteCollection
    {
        foreach ($group->getRoutes() as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRoute(RouteContract $route): MutableRouteCollection
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $routeName)
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $routeName) {
                return $route;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}