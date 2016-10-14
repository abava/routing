<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\RouteGroup as RouteGroupContract;

/**
 * Class RouteGroup
 *
 * @package Venta\Routing
 */
class RouteGroup extends RouteCollection implements RouteGroupContract
{

    private $host = '';

    private $prefix = '/';

    private $scheme = '';

    /**
     * @param callable $callback
     * @return RouteGroupContract
     */
    public static function collect(callable $callback): RouteGroupContract
    {
        $group = new static();
        $callback($group);

        return $group;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        $routes = [];
        /** @var Route $route */
        foreach (parent::getRoutes() as $route) {
            if (!$route->getHost() && $this->host) {
                $route = $route->withHost($this->host);
            }
            if (!$route->getScheme() && $this->scheme) {
                $route = $route->withScheme($this->scheme);
            }
            $routes[] = $route->withPathPrefix($this->prefix);
        }

        return $routes;
    }

    /**
     * @inheritDoc
     */
    public function setHost(string $host): RouteGroupContract
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPrefix(string $prefix): RouteGroupContract
    {
        $this->prefix = '/' . trim($prefix, '/');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScheme(string $scheme): RouteGroupContract
    {
        $this->scheme = $scheme;

        return $this;
    }


}