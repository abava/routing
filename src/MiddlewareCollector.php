<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Http\Contract\{
    RequestContract, ResponseContract
};
use Venta\Routing\Contract\MiddlewareContract;

/**
 * Class MiddlewareCollector
 *
 * @package Venta\Routing
 */
class MiddlewareCollector
{
    /** @var array|MiddlewareContract[] */
    protected $middlewares = [];

    /**
     * Universal method to add middleware
     *
     * @param $name
     * @param $middleware
     */
    public function addMiddleware($name, $middleware)
    {
        if ($middleware instanceof MiddlewareContract) {
            $this->addContractMiddleware($name, $middleware);
        }
        elseif (is_callable($middleware)) {
            $this->addCallableMiddleware($name, $middleware);
        }
        else {
            throw new \InvalidArgumentException('Middleware must either implement MiddlewareContract or be callable');
        }
    }

    /**
     * Adds middleware to collection straightforward
     *
     * @param                    $name
     * @param MiddlewareContract $middleware
     */
    public function addContractMiddleware($name, MiddlewareContract $middleware)
    {
        $this->middlewares[$name] = $middleware;
    }

    /**
     * Wraps callable (e.g. closure) with anonymous class that implements MiddlewareContract
     * Does not check if callable's typehinting fits MiddlewareContract's handle method.
     *
     * @param          $name
     * @param callable $callable
     */
    public function addCallableMiddleware($name, callable $callable)
    {
        $this->middlewares[$name] = new class($callable) implements MiddlewareContract {

            /** @var callable */
            protected $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function handle(RequestContract $request, \Closure $next): ResponseContract
            {
                $middleware = $this->callable;
                return $middleware($request, $next);
            }

        };
    }

    /**
     * @return array|Contract\MiddlewareContract[]
     */
    public function getMiddlewares()
    {
        return array_reverse($this->middlewares);
    }

}