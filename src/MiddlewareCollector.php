<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Http\Contract\{
    Request, Response
};
use Abava\Routing\Contract\Middleware;

/**
 * Class MiddlewareCollector
 *
 * @package Abava\Routing
 */
class MiddlewareCollector
{
    /** @var array|Middleware[] */
    protected $middlewares = [];

    /**
     * Universal method to add middleware
     *
     * @param string $name
     * @param callable|Middleware $middleware
     */
    public function addMiddleware(string $name, $middleware)
    {
        if ($middleware instanceof Middleware) {
            $this->addContractMiddleware($name, $middleware);
        } elseif (is_callable($middleware)) {
            $this->addCallableMiddleware($name, $middleware);
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * Adds middleware to collection straightforward
     *
     * @param string $name
     * @param Middleware $middleware
     */
    public function addContractMiddleware(string $name, Middleware $middleware)
    {
        $this->middlewares[$name] = $middleware;
    }

    /**
     * Wraps callable (e.g. closure) with anonymous class that implements Middleware contract
     * Does not check if callable's typehinting fits Middleware contract's handle method.
     *
     * @param string $name
     * @param callable $callable
     */
    public function addCallableMiddleware(string $name, callable $callable)
    {
        $this->middlewares[$name] = new class($callable) implements Middleware
        {

            /** @var callable */
            protected $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function handle(Request $request, \Closure $next): Response
            {
                $middleware = $this->callable;
                return $middleware($request, $next);
            }

        };
    }

    /**
     * @return array|Contract\Middleware[]
     */
    public function getMiddlewares()
    {
        return array_reverse($this->middlewares);
    }

}