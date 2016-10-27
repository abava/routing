<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteDispatcher as RouteDispatcherContract;
use Venta\Contracts\Routing\RouteDispatcherFactory as RouteDispatcherFactoryContract;
use Venta\Routing\RouteDispatcher;

/**
 * Class RouteDispatcherFactory
 *
 * @package Venta\Routing\Factory
 */
final class RouteDispatcherFactory implements RouteDispatcherFactoryContract
{
    /**
     * @var Container
     */
    private $container;

    /**
     * RouteDispatcherFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function create(RouteContract $route): RouteDispatcherContract
    {
        return new RouteDispatcher($route, $this->container);
    }


}