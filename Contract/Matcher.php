<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

use Venta\Routing\Route;
use Psr\Http\Message\RequestInterface;

/**
 * Interface Matcher
 *
 * @package Venta\Routing\Contracts
 */
interface Matcher
{

    /**
     * Finds route matching provided request
     *
     * @param RequestInterface $request
     * @param Collector $collector
     * @return Route
     */
    public function match(RequestInterface $request, Collector $collector): Route;

}