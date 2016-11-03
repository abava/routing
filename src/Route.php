<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route as RouteContract;

/**
 * Class Route
 *
 * @package Venta\Routing
 */
class Route implements RouteContract
{
    /**
     * @var string
     */
    private $domain = '';

    /**
     * Host to apply route to.
     *
     * @var string
     */
    private $host = '';

    /**
     * @var string
     */
    private $input = '';

    /**
     * Route allowed methods.
     *
     * @var string[]
     */
    private $methods = [];

    /**
     * List of middleware class names.
     *
     * @var string[]
     */
    private $middlewares = [];

    /**
     * Route name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Route path
     *
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $responder;

    /**
     * Scheme to apply route to.
     *
     * @var string
     */
    private $scheme = '';

    /**
     * Route variables.
     *
     * @var string[]
     */
    private $variables = [];

    /**
     * Route constructor.
     *
     * @param array $methods
     * @param string $path
     * @param string $responder
     */
    public function __construct(array $methods, string $path, string $responder)
    {
        $this->methods = $methods;
        $this->path = '/' . ltrim($path, '/');
        $this->responder = $responder;
    }

    /**
     * @param $path
     * @param callable|string $handler
     * @return Route
     */
    public static function any($path, $handler): Route
    {
        return new static(['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function delete(string $path, $handler): Route
    {
        return new static(['DELETE'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function get(string $path, $handler): Route
    {
        return new static(['GET'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function head(string $path, $handler): Route
    {
        return new static(['HEAD'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function options(string $path, $handler): Route
    {
        return new static(['OPTIONS'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function patch(string $path, $handler): Route
    {
        return new static(['PATCH'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function post(string $path, $handler): Route
    {
        return new static(['POST'], $path, $handler);
    }

    /**
     * @param string $path
     * @param callable|string $handler
     * @return Route
     */
    public static function put(string $path, $handler): Route
    {
        return new static(['PUT'], $path, $handler);
    }

    /**
     * @inheritDoc
     */
    public function getDomain():string
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getResponder(): string
    {
        return $this->getResponder();
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @inheritDoc
     */
    public function withDomain(string $domainClass): RouteContract
    {
        $route = clone $this;
        $route->domain = $domainClass;

        return $route;
    }

    /**
     * Set the host.
     *
     * @param string $host
     * @return RouteContract
     */
    public function withHost(string $host): RouteContract
    {
        $route = clone $this;
        $route->host = $host;

        return $route;
    }

    /**
     * @inheritDoc
     */
    public function withInput(string $inputClass): RouteContract
    {
        $route = clone $this;
        $route->input = $inputClass;

        return $route;
    }

    /**
     * @param string $middleware Middleware class name
     * @return RouteContract
     */
    public function withMiddleware(string $middleware): RouteContract
    {
        $route = clone $this;
        $route->middlewares[] = $middleware;

        return $route;
    }

    /**
     * Set the name.
     *
     * @param string $name
     * @return RouteContract
     */
    public function withName(string $name): RouteContract
    {
        $route = clone $this;
        $route->name = $name;

        return $route;
    }

    /**
     * Prefix the path.
     *
     * @param string $prefix
     * @return RouteContract
     */
    public function withPathPrefix(string $prefix): RouteContract
    {
        $route = clone $this;
        $route->path = $prefix == '/' || $prefix == '' ?
            $route->path :
            sprintf('/%s/%s', trim($prefix, '/'), ltrim($route->path, '/'));

        return $route;
    }

    /**
     * Set the scheme.
     *
     * @param string $scheme
     * @return RouteContract
     */
    public function withScheme(string $scheme): RouteContract
    {
        $route = clone $this;
        $route->scheme = $scheme;

        return $route;
    }

    /**
     * Set route parameters.
     *
     * @param array $variables
     * @return RouteContract
     */
    public function withVariables(array $variables): RouteContract
    {
        $route = clone $this;
        $route->variables = $variables;

        return $route;
    }

}