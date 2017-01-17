<?php

namespace spec\Venta\Routing\Factory;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Container\Invoker;
use Venta\Contracts\Routing\Route;
use Venta\Routing\RouteDispatcher;

class RouteDispatcherFactorySpec extends ObjectBehavior
{

    function let(Invoker $invoker)
    {
        $this->beConstructedWith($invoker);
    }

    function it_creates_route_dispatcher(Route $route, Invoker $invoker)
    {
        $this->create($route)->shouldBeLike(
            new RouteDispatcher($invoker->getWrappedObject(), $route->getWrappedObject())
        );
    }

    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\RouteDispatcherFactory::class);
    }
}

