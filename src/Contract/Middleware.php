<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Http\Contract\{
    Request, Response
};

/**
 * Interface Middleware
 *
 * @package Abava\Routing\Contract
 */
interface Middleware
{
    /**
     * Function, called on middleware execution
     *
     * @param Request $request
     * @param \Closure next
     * @return Response
     */
    public function handle(Request $request, \Closure $next) : Response;

}