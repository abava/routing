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

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var string
     */
    private $prefix = '/';

    /**
     * @var string
     */
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
    public function all(): array
    {
        $routes = [];
        /** @var Route $route */
        foreach (parent::all() as $route) {
            if (!$route->host() && $this->host) {
                $route = $route->withHost($this->host);
            }
            if (!$route->scheme() && $this->scheme) {
                $route = $route->secure();
            }
            $routes[] = $route->withPath($this->addPathPrefix($route->path()));
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

    /**
     * Prepends group prefix to provided route $path.
     *
     * @param string $path
     * @return string
     */
    private function addPathPrefix(string $path): string
    {
        return $this->prefix == '/' || $this->prefix == '' ? $path :
            sprintf('/%s/%s', trim($this->prefix, '/'), ltrim($path, '/'));
    }

}