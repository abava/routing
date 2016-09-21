<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Routing\Contract\Middleware;

/**
 * Class Builder
 *
 * @package Venta\Routing
 */
class Builder
{
    /**
     * @var string|callable
     */
    protected $action;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var array
     */
    protected $middleware;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * Builder constructor.
     *
     * @param array $methods
     * @param string $path
     * @param callable|string $action
     */
    public function __construct(array $methods, string $path, $action)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->action = $action;
    }

    /**
     * @param $path
     * @param callable|string $action
     * @return Builder
     */
    public static function any($path, $action): Builder
    {
        return new static(['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'], $path, $action);
    }

    /**
     * @param array $methods
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function create(array $methods, string $path, $action): Builder
    {
        return new static($methods, $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function delete(string $path, $action): Builder
    {
        return new static(['DELETE'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function get(string $path, $action): Builder
    {
        return new static(['GET'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function head(string $path, $action): Builder
    {
        return new static(['HEAD'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function options(string $path, $action): Builder
    {
        return new static(['OPTIONS'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function patch(string $path, $action): Builder
    {
        return new static(['PATCH'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function post(string $path, $action): Builder
    {
        return new static(['POST'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return Builder
     */
    public static function put(string $path, $action): Builder
    {
        return new static(['PUT'], $path, $action);
    }

    /**
     * @param callable|string $action
     * @return Builder
     */
    public function action($action): Builder
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return Route
     */
    public function build(): Route
    {
        $route = new Route($this->methods, $this->path, $this->action);
        if ($this->name) {
            $route = $route->withName($this->name);
        }
        if ($this->host) {
            $route = $route->withHost($this->host);
        }
        if ($this->scheme) {
            $route = $route->withScheme($this->scheme);
        }
        if ($this->middleware) {
            foreach ($this->middleware as $name => $middleware) {
                $route = $route->withMiddleware($name, $middleware);
            }
        }
        // Name must be unique per route, clear for later use
        $this->name = null;

        return $route;
    }

    /**
     * @param string $host
     * @return Builder
     */
    public function host(string $host): Builder
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param array ...$method
     * @return Builder
     */
    public function method(...$method): Builder
    {
        $this->methods = $method;

        return $this;
    }

    /**
     * @param string $name
     * @param string|callable|Middleware $middleware
     * @return Builder
     */
    public function middleware(string $name, $middleware): Builder
    {
        $this->middleware[$name] = $middleware;

        return $this;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function name(string $name): Builder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $path
     * @return Builder
     */
    public function path(string $path): Builder
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $scheme
     * @return Builder
     */
    public function scheme(string $scheme): Builder
    {
        $this->scheme = $scheme;

        return $this;
    }

}