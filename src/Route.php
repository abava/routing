<?php declare(strict_types = 1);

namespace Venta\Routing;

use InvalidArgumentException;
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
     * @param string[] $methods
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
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function any(string $path, $responder): Route
    {
        return new static(['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function delete(string $path, $responder): Route
    {
        return new static(['DELETE'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function get(string $path, $responder): Route
    {
        return new static(['GET'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function head(string $path, $responder): Route
    {
        return new static(['HEAD'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function options(string $path, $responder): Route
    {
        return new static(['OPTIONS'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function patch(string $path, $responder): Route
    {
        return new static(['PATCH'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function post(string $path, $responder): Route
    {
        return new static(['POST'], $path, $responder);
    }

    /**
     * @param string $path
     * @param callable|string $responder
     * @return Route
     */
    public static function put(string $path, $responder): Route
    {
        return new static(['PUT'], $path, $responder);
    }

    /**
     * @inheritDoc
     */
    public function compilePath(array $variables = []): string
    {
        $path = $this->getPath();
        foreach ($variables as $key => $value) {
            $pattern = sprintf('~%s~x', sprintf('\{\s*%s\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}]*)*))?\}', preg_quote($key)));
            preg_match($pattern, $path, $matches);
            if (isset($matches[1]) && !preg_match('/' . $matches[1] . '/', (string)$value)) {
                throw new InvalidArgumentException(
                    sprintf('Substitution value "%s" does not match "%s" parameter "%s" pattern.',
                        $value, $key, $matches[1]
                    )
                );
            }
            $path = preg_replace($pattern, $value, $path);
        }
        // 1. remove patterns for named prameters
        // 2. remove optional segments' ending delimiters
        // 3. split path into an array of optional segments and remove those
        //    containing unsubstituted parameters starting from the last segment
        $path = preg_replace('/{(\w+):(.+?)}/', '{$1}', $path);
        $path = str_replace(']', '', $path);
        $segments = array_reverse(explode('[', $path));
        foreach ($segments as $n => $segment) {
            if (strpos($segment, '{') !== false) {
                if (isset($segments[$n - 1])) {
                    throw new InvalidArgumentException(
                        'Optional segments with unsubstituted parameters cannot '
                        . 'contain segments with substituted parameters when using FastRoute'
                    );
                }
                unset($segments[$n]);
                if (count($segments) == 0) {
                    preg_match('/{.+}/', $segment, $params);
                    throw new InvalidArgumentException(
                        sprintf('Parameter "%s" is mandatory', $params[0] ?? $segment)
                    );
                }
            }
        }
        $path = implode('', array_reverse($segments));

        return $path;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): string
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
        return $this->responder;
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
    public function secure(): RouteContract
    {
        $route = clone $this;
        $route->scheme = 'https';

        return $route;
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function withMiddleware(string $middleware): RouteContract
    {
        $route = clone $this;
        $route->middlewares[] = $middleware;

        return $route;
    }

    /**
     * @inheritDoc
     */
    public function withName(string $name): RouteContract
    {
        $route = clone $this;
        $route->name = $name;

        return $route;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function withVariables(array $variables): RouteContract
    {
        $route = clone $this;
        $route->variables = $variables;

        return $route;
    }

}