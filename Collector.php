<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Routing\Contract\Collector as CollectorContract;
use Venta\Routing\Contract\Group as GroupRouteCollectorContract;
use Venta\Routing\Contract\UrlGenerator;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;

/**
 * Class RouteCollector
 *
 * @package Venta\Routing
 */
class Collector extends RouteCollector implements CollectorContract, UrlGenerator
{

    /**
     * Route groups
     *
     * @var Group[]
     */
    protected $groups = [];

    /**
     * Captured routes
     *
     * @var Route[]
     */
    protected $routes = [];

    /**
     * {@inheritdoc}
     */
    public function add(Route $route)
    {
        if ($route->getName()) {
            $this->routes[$route->getName()] = $route;
        } else {
            $this->routes[] = $route;
        }
    }

    /**
     * Adds route directly to parser / data-generator
     *
     * @param array $httpMethod
     * @param string $route
     * @param mixed $handler
     * @return void
     */
    public function addRoute($httpMethod, $route, $handler)
    {
        $this->routes[] = new Route((array)$httpMethod, $route, $handler);
    }

    /**
     * Returns ALL routes
     *
     * @return array
     */
    public function getData()
    {
        $this->collectGroups();
        foreach ($this->routes as $route) {
            // Simply pass all routes to collection
            // This won't filter host / scheme routes
            parent::addRoute($route->getMethods(), $route->getPath(), $route);
        }

        return parent::getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredData(RequestInterface $request): array
    {
        $this->collectGroups();
        foreach ($this->routes as $route) {

            // As FastRoute cannot match neither by host nor by scheme
            // we have to filter these routes manually
            // before we pass them to FastRoute's DataGenerator

            // Validate host
            if ($route->getHost() && $route->getHost() !== $request->getUri()->getHost()) {
                continue;
            }

            // Validate scheme
            if ($route->getScheme() && $route->getScheme() !== $request->getUri()->getScheme()) {
                continue;
            }

            // Pass route for parsing and data generation
            parent::addRoute($route->getMethods(), $route->getPath(), $route);
        }

        return parent::getData();
    }

    /**
     * Returns all route instances
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        $this->collectGroups();

        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function group(string $prefix, callable $callback): GroupRouteCollectorContract
    {
        $group = new Group($prefix, $callback, $this);

        $this->groups[] = $group;

        return $group;
    }

    /**
     * Generate a URI based on a given route.
     *
     * Replacements in FastRoute are written as `{name}` or `{name:<pattern>}`;
     * this method uses a regular expression to search for substitutions that
     * match, and replaces them with the value provided.
     *
     *
     * @param string $name Route name.
     * @param array $substitutions Key/value pairs to substitute into the route pattern.
     * @return string URI path generated.
     * @throws \InvalidArgumentException
     */
    public function url(string $name, array $substitutions = []): string
    {
        $routes = $this->getRoutes();
        if (!isset($routes[$name])) {
            throw new \InvalidArgumentException("Route '$name' not found");
        }
        $route = $routes[$name];

        return $route->url($substitutions);
    }

    /**
     * Collect routes from groups
     *
     * @return void
     */
    protected function collectGroups()
    {
        do {
            // Groups may add other groups,
            // so we need to iterate through array
            // until no groups remain
            foreach ($this->groups as $key => $group) {
                $group->collect();
                // delete group to prevent route duplication
                unset($this->groups[$key]);
            }
        } while (count($this->groups) > 0);
    }

}