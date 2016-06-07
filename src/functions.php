<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteParser\Std;

/**
 * Function to create router instance
 *
 * @param  callable $definer
 * @return Router
 */
function createRouter(callable $definer)
{
    $collector = new RoutesCollector(new Std, new GroupCountBased);
    $definer($collector);

    return new Router($collector->getData());
}