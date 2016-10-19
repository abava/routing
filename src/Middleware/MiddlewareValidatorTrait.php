<?php namespace Venta\Routing\Middleware;

use Venta\Contracts\Routing\Middleware;

/**
 * Class MiddlewareValidatorTrait
 *
 * @package Venta\Routing\Middleware
 */
trait MiddlewareValidatorTrait
{

    /**
     * Check if provided argument may be used as middleware
     *
     * @param $middleware
     * @return bool
     */
    public function isValidMiddleware($middleware): bool
    {
        return is_string($middleware) && is_subclass_of($middleware, Middleware::class)
               || $middleware instanceof Middleware
               || is_callable($middleware);
    }

}