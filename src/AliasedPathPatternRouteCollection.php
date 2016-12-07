<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\RouteGroup as RouteGroupContract;

/**
 * Class SugaredRouteCollection
 *
 * @package Venta\Routing
 */
class AliasedPathPatternRouteCollection implements RouteCollectionContract
{

    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * SugaredRouteCollection constructor.
     *
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
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
        $this->routes->addRoute($route->withPath($this->replaceRegexAliases($route->getPath())));

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

    /**
     * Returns aliases to replace. Overwrite this method to remove / add your aliases.
     *
     * @return array
     */
    protected function getRegexAliases(): array
    {
        return [
            '/{(.+?):number}/' => '{$1:[0-9]+}',
            '/{(.+?):word}/' => '{$1:[a-zA-Z]+}',
            '/{(.+?):alphanum}/' => '{$1:[a-zA-Z0-9-_]+}',
            '/{(.+?):slug}/' => '{$1:[a-z0-9-]+}',
        ];
    }

    /**
     * Replaces regex aliases within provided $path.
     *
     * @param string $path
     * @return string
     */
    private function replaceRegexAliases(string $path): string
    {
        $regexAliases = $this->getRegexAliases();

        return preg_replace(array_keys($regexAliases), array_values($regexAliases), $path);
    }

}