<?php declare(strict_types = 1);

namespace Abava\Routing;

/**
 * Class Builder
 *
 * @package Abava\Routing
 */
class Builder
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var string|callable
     */
    protected $action;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var array
     */
    protected $middleware;

    /**
     * @var string
     */
    protected $name;

    /**
     * Builder constructor.
     *
     * @param array $methods
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param string $path
     * @return Builder
     */
    public function url(string $path): Builder
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param array $methods
     * @return Builder
     */
    public function methods(array $methods): Builder
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @param callable|string $action
     * @return Builder
     */
    public function to($action): Builder
    {
        $this->action = $action;

        return $this;
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
     * @param string $scheme
     * @return Builder
     */
    public function scheme(string $scheme): Builder
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $name
     * @param $middleware
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
     * @param string $path
     * @return Builder
     */
    public static function get(string $path): Builder
    {
        return (new static(['GET']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function post(string $path): Builder
    {
        return (new static(['POST']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function put(string $path): Builder
    {
        return (new static(['PUT']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function patch(string $path): Builder
    {
        return (new static(['PATCH']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function options(string $path): Builder
    {
        return (new static(['OPTIONS']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function delete(string $path): Builder
    {
        return (new static(['DELETE']))->url($path);
    }

    /**
     * @param string $path
     * @return Builder
     */
    public static function head(string $path): Builder
    {
        return (new static(['HEAD']))->url($path);
    }

    /**
     * @param $path
     * @return Builder
     */
    public static function any($path): Builder
    {
        return (new static(['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE']))->url($path);
    }

}