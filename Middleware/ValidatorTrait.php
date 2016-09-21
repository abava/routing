<?php namespace Venta\Routing\Middleware;

use Venta\Routing\Contract\Middleware;

/**
 * Trait ValidatorTrait
 *
 * @package Venta\Routing\Middleware
 */
trait ValidatorTrait
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