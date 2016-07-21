<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Routing\Route;
use Psr\Http\Message\RequestInterface;

/**
 * Interface Matcher
 *
 * @package Abava\Routing\Contract
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