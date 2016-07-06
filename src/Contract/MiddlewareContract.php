<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

use Venta\Http\Contract\{
    RequestContract, ResponseContract
};

/**
 * Interface MiddlewareContract
 *
 * @package Venta\Routing
 */
interface MiddlewareContract
{
    /**
     * Function, called on middleware execution
     *
     * @param RequestContract $request
     * @param \Closure        next
     * @return ResponseContract
     */
    public function handle(RequestContract $request, \Closure $next) : ResponseContract;

}