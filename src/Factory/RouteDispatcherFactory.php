<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use Venta\Contracts\Container\Invoker;
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
     * @var Invoker
     */
    private $invoker;

    /**
     * RouteDispatcherFactory constructor.
     *
     * @param Invoker $invoker
     */
    public function __construct(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * @inheritDoc
     */
    public function create(RouteContract $route): RouteDispatcherContract
    {
        return new RouteDispatcher($this->invoker, $route);
    }


}