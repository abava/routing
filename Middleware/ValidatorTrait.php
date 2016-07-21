<?php namespace Abava\Routing\Middleware;

use Abava\Routing\Contract\Middleware;

/**
 * Trait ValidatorTrait
 *
 * @package Abava\Routing\Middleware
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
        return is_string($middleware) && is_subclass_of($middleware, Middleware::class) ||
               $middleware instanceof Middleware ||
               is_callable($middleware);
    }

}