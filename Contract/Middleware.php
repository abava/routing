<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface Middleware
 *
 * @package Venta\Routing\Contracts
 */
interface Middleware
{
    /**
     * Function, called on middleware execution
     *
     * @param RequestInterface $request
     * @param \Closure next
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request, \Closure $next) : ResponseInterface;

}