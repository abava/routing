<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\RouteCollector;
use Venta\Contracts\Routing\RouteParser as RouteParserContract;

/**
 * Class RouteParser
 *
 * @package Venta\Routing
 */
final class RouteParser implements RouteParserContract
{

    /**
     * @var RouteCollector
     */
    private $collector;

    /**
     * RouteParser constructor.
     *
     * @param RouteCollector $collector
     */
    public function __construct(RouteCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @inheritDoc
     */
    public function parse(array $routes): array
    {
        /** @var \Venta\Contracts\Routing\Route $route */
        foreach ($routes as $route) {
            $this->collector->addRoute($route->getMethods(), $route->getPath(), $route);
        }

        return $this->collector->getData();
    }

}
