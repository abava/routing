<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

use \Psr\Http\Message\{RequestInterface, ResponseInterface};

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
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Closure                           $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, \Closure $next) : ResponseInterface;
}