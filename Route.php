<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Routing\Contract\Middleware;
use Abava\Routing\Contract\UrlBuilder;
use Abava\Routing\Middleware\ValidatorTrait;

/**
 * Class Route
 * Is immutable Value-Object
 *
 * @package Abava\Routing
 */
class Route implements UrlBuilder
{

    use ValidatorTrait;

    /**
     * Route handle, may contain callable or controller action
     *
     * @var string|callable
     */
    protected $callable;

    /**
     * Route allowed methods
     *
     * @var string[]
     */
    protected $methods = [];

    /**
     * Route path
     *
     * @var string
     */
    protected $path = '';

    /**
     * Host to apply route to
     *
     * @var string
     */
    protected $host = '';

    /**
     * Scheme to apply route to
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * Route name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Route parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Route middlewares
     *
     * @var Middleware[]
     */
    protected $middlewares = [];

    /**
     * Route constructor.
     *
     * @param array $methods
     * @param string $path
     * @param $callable
     */
    public function __construct(array $methods, string $path, $callable)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->callable = $callable;
    }

    /**
     * Get the callable.
     *
     * @return string|callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the methods.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get the host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Get the scheme
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the host
     *
     * @param string $host
     * @return Route
     */
    public function withHost(string $host): Route
    {
        $route = clone $this;
        $route->host = $host;

        return $route;
    }

    /**
     * Set the scheme
     *
     * @param string $scheme
     * @return Route
     */
    public function withScheme(string $scheme): Route
    {
        $route = clone $this;
        $route->scheme = $scheme;

        return $route;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return Route
     */
    public function withName(string $name): Route
    {
        $route = clone $this;
        $route->name = $name;

        return $route;
    }

    /**
     * Get route parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Set route parameters
     *
     * @param array $parameters
     * @return Route
     */
    public function withParameters(array $parameters): Route
    {
        $route = clone $this;
        $route->parameters = $parameters;

        return $route;
    }

    /**
     * Get route specific middleware array
     *
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Add middleware to route
     *
     * @param string $name
     * @param $middleware
     * @return Route
     */
    public function withMiddleware(string $name, $middleware): Route
    {
        if ($this->isValidMiddleware($middleware)) {
            $route = clone $this;
            $route->middlewares[$name] = $middleware;

            return $route;
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * Set the path
     *
     * @param string $path
     * @return Route
     */
    public function withPath(string $path): Route
    {
        $route = clone $this;
        $route->path = $path;

        return $route;
    }

    /**
     * Replacements in FastRoute are written as `{name}` or `{name:<pattern>}`;
     * this method uses a regular expression to search for substitutions that
     * match, and replaces them with the value provided.
     *
     * @inheritDoc
     */
    public function url(array $parameters = []): string
    {
        $path = Parser::replacePatternMatchers($this->getPath());
        foreach ($parameters as $key => $value) {
            $pattern = sprintf(
                '~%s~x',
                sprintf('\{\s*%s\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}]*)*))?\}', preg_quote($key))
            );
            preg_match($pattern, $path, $matches);
            if (isset($matches[1]) && !preg_match('/' . $matches[1] . '/', (string)$value)) {
                throw new \InvalidArgumentException(
                    "Substitution value '$value' does not match '$key' parameter '{$matches[1]}' pattern."
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
                if (count($segs) == 0) {
                    preg_match('/{.+}/', $seg, $params);
                    $mandatory = $params[0] ?? $seg;
                    throw new \InvalidArgumentException("Parameter '$mandatory' is mandatory");
                }
            }
        }
        $path = implode('', array_reverse($segs));

        return $path;
    }

}