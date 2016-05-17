<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\RouteCollector;

/**
 * Class RoutesCollector
 *
 * @package Venta\Routing
 */
class RoutesCollector extends RouteCollector
{
    /**
     * Register GET route
     *
     * @param string $route
     * @param        $handle
     */
    public function get(string $route, $handle)
    {
        $this->addRoute('GET', $route, $handle);
    }
}