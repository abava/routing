<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Routing\Contract\Collector as CollectorContract;
use Abava\Routing\Contract\Group as GroupRouteCollectorContract;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;

/**
 * Class RouteCollector
 *
 * @package Abava\Routing
 */
class Collector extends RouteCollector implements CollectorContract
{
    use CollectorTrait;

    /**
     * Captured routes
     *
     * @var Route[]
     */
    protected $routes = [];

    /**
     * Route groups
     *
     * @var Group[]
     */
    protected $groups = [];

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
     * {@inheritdoc}
     */
    public function add(Route $route)
    {
        $this->routes[] = $route;
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
     * Collect routes from groups
     *
     * @return void
     */
    protected function collectGroups()
    {
        foreach ($this->groups as $key => $group) {
            $group->collect();
            // delete group to prevent route duplication
            unset($this->groups[$key]);
        }
    }


}