<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Routing\Contract\Collector as CollectorContract;
use Abava\Routing\Contract\Group as GroupRouteCollectorContract;
use Abava\Routing\Contract\UrlGenerator;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;

/**
 * Class RouteCollector
 *
 * @package Abava\Routing
 */
class Collector extends RouteCollector implements CollectorContract, UrlGenerator
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
        if ($route->getName()) {
            $this->routes[$route->getName()] = $route;
        } else {
            $this->routes[] = $route;
        }
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
            throw new \InvalidArgumentException('');
        }
        $route = $routes[$name];
        $path = Parser::replacePatternMatchers($route->getPath());
        foreach ($substitutions as $key => $value) {
            $pattern = sprintf(
                '~%s~x',
                sprintf('\{\s*%s\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}]*)*))?\}', preg_quote($key))
            );
            preg_match($pattern, $path, $matches);
            if (isset($matches[1]) && !preg_match('/'.$matches[1].'/', $value)) {
                throw new \InvalidArgumentException(
                    "Substitution value '$value' does not match '$key' parameter '{$matches[1]}' pattern."
                );
            }
            $path = preg_replace($pattern, $value, $path);
        }
        // 1. remove optional segments' ending delimiters
        // 2. split path into an array of optional segments and remove those
        //    containing unsubstituted parameters starting from the last segment
        $path = str_replace(']', '', $path);
        $segs = array_reverse(explode('[', $path));
        foreach ($segs as $n => $seg) {
            if (strpos($seg, '{') !== false) {
                if (isset($segs[$n - 1])) {
                    throw new \InvalidArgumentException(
                        'Optional segments with unsubstituted parameters cannot '
                        . 'contain segments with substituted parameters when using FastRoute'
                    );
                }
                unset($segs[$n]);
            }
        }
        $path = implode('', array_reverse($segs));
        return $path;
    }

}