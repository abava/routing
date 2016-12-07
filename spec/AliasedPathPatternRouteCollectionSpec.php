<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;

class AliasedPathPatternRouteCollectionSpec extends ObjectBehavior
{
    function let(RouteCollection $routes)
    {
        $this->beConstructedWith($routes);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(RouteCollection::class);
    }

    function it_replaces_regex_placeholder_aliases(RouteCollection $routes, Route $route)
    {
        $route->getPath()->willReturn('{id:number}');
        $route->withPath('{id:[0-9]+}')->willReturn($route)->shouldBeCalled();
        $routes->addRoute($route)->willReturn($routes)->shouldBeCalled();

        $this->addRoute($route)->shouldBeAnInstanceOf(RouteCollection::class);
    }
}
