<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Http\Contract\{
    RequestContract, ResponseContract
};

/**
 * Interface MiddlewareContract
 *
 * @package Abava\Routing
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