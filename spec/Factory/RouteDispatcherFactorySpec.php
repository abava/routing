<?php

namespace spec\Venta\Routing\Factory;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Route;
use Venta\Routing\RouteDispatcher;

class RouteDispatcherFactorySpec extends ObjectBehavior
{

    function let(Container $container)
    {
        $this->beConstructedWith($container);
    }

    function it_creates_route_dispatcher(Route $route, Container $container)
    {
        $this->create($route)->shouldBeLike(
            new RouteDispatcher($route->getWrappedObject(), $container->getWrappedObject())
        );
    }

    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\RouteDispatcherFactory::class);
    }
}

