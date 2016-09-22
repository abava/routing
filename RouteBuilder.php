<?php declare(strict_types = 1);

namespace Venta\Routing;

/**
 * Class RouteBuilder.
 *
 * @package Venta\Routing
 */
class RouteBuilder
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
     * @return RouteBuilder
     */
    public static function any($path, $action): RouteBuilder
    {
        return new static(['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'], $path, $action);
    }

    /**
     * @param array $methods
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function create(array $methods, string $path, $action): RouteBuilder
    {
        return new static($methods, $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function delete(string $path, $action): RouteBuilder
    {
        return new static(['DELETE'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function get(string $path, $action): RouteBuilder
    {
        return new static(['GET'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function head(string $path, $action): RouteBuilder
    {
        return new static(['HEAD'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function options(string $path, $action): RouteBuilder
    {
        return new static(['OPTIONS'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function patch(string $path, $action): RouteBuilder
    {
        return new static(['PATCH'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function post(string $path, $action): RouteBuilder
    {
        return new static(['POST'], $path, $action);
    }

    /**
     * @param string $path
     * @param callable|string $action
     * @return RouteBuilder
     */
    public static function put(string $path, $action): RouteBuilder
    {
        return new static(['PUT'], $path, $action);
    }

    /**
     * @param callable|string $action
     * @return RouteBuilder
     */
    public function action($action): RouteBuilder
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
     * @return RouteBuilder
     */
    public function host(string $host): RouteBuilder
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param array ...$method
     * @return RouteBuilder
     */
    public function method(...$method): RouteBuilder
    {
        $this->methods = $method;

        return $this;
    }

    /**
     * @param string $name
     * @param string|callable|\Venta\Contracts\Routing\Middleware $middleware
     * @return RouteBuilder
     */
    public function middleware(string $name, $middleware): RouteBuilder
    {
        $this->middleware[$name] = $middleware;

        return $this;
    }

    /**
     * @param string $name
     * @return RouteBuilder
     */
    public function name(string $name): RouteBuilder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $path
     * @return RouteBuilder
     */
    public function path(string $path): RouteBuilder
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $scheme
     * @return RouteBuilder
     */
    public function scheme(string $scheme): RouteBuilder
    {
        $this->scheme = $scheme;

        return $this;
    }

}